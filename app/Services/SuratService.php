<?php


namespace App\Services;

use App\Models\{LogAktivitas, PengaturanSekolah, SuratKeluar, User};
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Output\QROutputInterface;
use Illuminate\Support\Facades\{DB, Storage};
use Illuminate\Support\Str;
use App\Models\SuratMasuk;

class SuratService
{
    const ROMAWI = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

    public function romawi(Carbon $t): string
    {
        return self::ROMAWI[$t->month - 1];
    }

    /** Nomor urut berikutnya untuk satu tahun (reset tiap tahun). */
    public function nomorBerikutnya(int $tahun, bool $lock = false): int
    {
        $q = SuratKeluar::where('tahun', $tahun)->whereNotNull('nomor_urut')->orderByDesc('nomor_urut');
        if ($lock) {
            $q->lockForUpdate();
        }
        $max = $q->value('nomor_urut');
        if ($max) {
            return $max + 1;
        }
        // tahun baru -> mulai 1, kecuali sistem benar-benar baru -> pakai nomor_awal
        $adaNomor = SuratKeluar::whereNotNull('nomor_urut')->exists();
        return $adaNomor ? 1 : (int) PengaturanSekolah::ambil('nomor_awal', 1);
    }

    public function isiTurunan(SuratKeluar $s): void
    {
        $s->bulan_romawi = $this->romawi($s->tgl_surat);
        $s->tahun = $s->tgl_surat->year;
    }

    public function nomorLengkap(SuratKeluar $s, int $urut): string
    {
        return $urut . '/SMK-Kosgoro/' . $s->klasifikasi->kode_indeks . '/' . $s->bulan_romawi . '/' . $s->tahun;
    }

    public function namaFile(SuratKeluar $s): string
    {
        $slug = implode('-', array_slice(explode('-', Str::slug($s->perihal)), 0, 6));
        return str_replace('/', '-', $s->nomor_lengkap) . ($slug ? '_' . $slug : '') . '.pdf';
    }

    public function finalisasi(SuratKeluar $s, User $u, ?string $catatan = null): void
    {
        abort_unless($s->status_surat === 'Draft', 422, 'Surat ini bukan draft.');

        DB::transaction(function () use ($s, $u, $catatan) {
            $s->load(['klasifikasi', 'penandatangan', 'detail']);
            $this->isiTurunan($s);
            $urut = $this->nomorBerikutnya($s->tahun, true);

            $s->fill([
                'nomor_urut'            => $urut,
                'nomor_lengkap'         => $this->nomorLengkap($s, $urut),
                'kode_verifikasi'       => $s->kode_verifikasi ?: $this->kodeUnik(),
                'acc_kepsek'            => true,
                'catatan_acc'           => $catatan,
                'tgl_acc'               => now(),
                'acc_dikonfirmasi_oleh' => $u->id_user,
                'status_surat'          => 'Final',
            ]);
            $s->nama_file = $this->namaFile($s);

            $bin  = $this->buatPdf($s)->output();
            $path = "surat/{$s->tahun}/{$s->nama_file}";
            Storage::disk('local')->put($path, $bin);

            $s->path_pdf     = $path;
            $s->hash_dokumen = hash('sha256', $bin);
            $s->save();

            $this->log($u, $s, 'finalisasi', 'Nomor ' . $s->nomor_lengkap);
        });
    }

    public function batalkan(SuratKeluar $s, User $u, string $alasan): void
    {
        $s->update(['status_surat' => 'Dibatalkan']);
        $this->log($u, $s, 'batal', $alasan);
    }

    public function buatPdf(SuratKeluar $s)
    {
        $kop = PengaturanSekolah::ambil();

        return Pdf::loadView('surat.pdf', [
            's'    => $s,
            'kop'  => $kop,
            'logo' => $this->dataUri($kop['logo'] ?? null),
            'ttd'  => $this->dataUri($s->penandatangan->file_ttd_stempel),
            'qr'   => $this->qr(route('verifikasi', $s->kode_verifikasi)),
        ])->setPaper('a4', 'portrait');
    }


    /** Kode unik 6 karakter, tanpa huruf/angka yang mudah tertukar (0, O, 1, I, L). */
public function kodeUnik(): string
{
    $abjad = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    do {
        $kode = '';
        for ($i = 0; $i < 6; $i++) {
            $kode .= $abjad[random_int(0, strlen($abjad) - 1)];
        }
    } while (
        SuratKeluar::where('kode_verifikasi', $kode)->exists()
        || SuratMasuk::where('kode_unik', $kode)->exists()
    );

    return $kode;
}
    public function log(?User $u, ?SuratKeluar $s, string $aksi, ?string $ket = null): void
    {
        LogAktivitas::create([
            'id_user'    => $u?->id_user,
            'id_surat'   => $s?->id_surat,
            'aksi'       => $aksi,
            'keterangan' => $ket,
        ]);
    }

    private function dataUri(?string $path): ?string
    {
        $disk = Storage::disk('public');
        if (!$path || !$disk->exists($path)) {
            return null;
        }
        return 'data:' . ($disk->mimeType($path) ?: 'image/png') . ';base64,' . base64_encode($disk->get($path));
    }

    private function qr(string $url): ?string
    {
        try {
            return (new QRCode(new QROptions([
                'outputType'   => QROutputInterface::GDIMAGE_PNG,
                'scale'        => 6,
                'outputBase64' => true,
            ])))->render($url);
        } catch (\Throwable $e) {
            report($e);
            return null; // PDF tetap jadi tanpa QR
        }
    }
} 