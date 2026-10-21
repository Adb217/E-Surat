<?php

namespace App\Http\Controllers;

use App\Models\{DetailKegiatan, KlasifikasiSurat, LogAktivitas, Pegawai, PengaturanSekolah, SuratKeluar};
use App\Services\SuratService;
use App\Support\Html;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Storage};

class SuratKeluarController extends Controller
{
    public function __construct(private SuratService $svc) {}

    public function create()
    {
        return view('surat.form', $this->formData(new SuratKeluar));
    }

    public function store(Request $r)
    {
        $data = $this->valid($r);
        if ($r->input('aksi') === 'final' && !$r->boolean('acc')) {
            return back()->withInput()->withErrors(['acc' => 'Centang konfirmasi persetujuan Kepala Sekolah dulu.']);
        }

        $s = $this->simpan(new SuratKeluar, $data, $r);
        $this->svc->log($r->user(), $s, 'buat');

        return $this->lanjut($r, $s);
    }

    public function show(SuratKeluar $surat)
    {
        $surat->load(['klasifikasi', 'penandatangan', 'pembuat', 'detail']);

        return view('surat.show', [
            'surat' => $surat,
            'logs'  => LogAktivitas::with('user')->where('id_surat', $surat->id_surat)->latest()->get(),
        ]);
    }

    public function edit(SuratKeluar $surat)
    {
        if ($surat->status_surat !== 'Draft') {
            return redirect()->route('surat.show', $surat)->withErrors(['x' => 'Surat yang sudah Final tidak bisa diedit.']);
        }
        $surat->load('detail');
        return view('surat.form', $this->formData($surat));
    }

    public function update(Request $r, SuratKeluar $surat)
    {
        abort_unless($surat->status_surat === 'Draft', 403);

        $data = $this->valid($r);
        if ($r->input('aksi') === 'final' && !$r->boolean('acc')) {
            return back()->withInput()->withErrors(['acc' => 'Centang konfirmasi persetujuan Kepala Sekolah dulu.']);
        }

        $s = $this->simpan($surat, $data, $r);
        $this->svc->log($r->user(), $s, 'ubah');

        return $this->lanjut($r, $s);
    }

    public function destroy(Request $r, SuratKeluar $surat)
    {
        abort_unless($surat->status_surat === 'Draft', 403);
        $this->svc->log($r->user(), null, 'hapus_draft', $surat->perihal);
        $surat->delete();

        return redirect()->route('surat.index')->with('ok', 'Draft dihapus.');
    }

    public function finalisasi(Request $r, SuratKeluar $surat)
    {
        $r->validate(
            ['acc' => 'accepted', 'catatan_acc' => 'nullable|string|max:255'],
            ['acc.accepted' => 'Centang konfirmasi persetujuan Kepala Sekolah dulu.']
        );

        try {
            $this->svc->finalisasi($surat, $r->user(), $r->catatan_acc);
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['pdf' => 'Gagal membuat PDF: ' . $e->getMessage()]);
        }

        return redirect()->route('surat.show', $surat)->with('ok', 'Surat final dengan nomor ' . $surat->nomor_lengkap);
    }

    public function batal(Request $r, SuratKeluar $surat)
    {
        abort_unless($surat->status_surat === 'Final', 403);
        $r->validate(['alasan' => 'required|string|max:255']);
        $this->svc->batalkan($surat, $r->user(), $r->alasan);

        return back()->with('ok', 'Surat dibatalkan. Nomor tetap tercatat dan tidak dipakai ulang.');
    }

    public function pdf(Request $r, SuratKeluar $surat)
    {
        abort_unless($surat->path_pdf && Storage::disk('local')->exists($surat->path_pdf), 404);

        return $r->boolean('unduh')
            ? Storage::disk('local')->download($surat->path_pdf, $surat->nama_file)
            : Storage::disk('local')->response($surat->path_pdf, $surat->nama_file, ['Content-Type' => 'application/pdf']);
    }

    // ---------- helper ----------

    private function formData(SuratKeluar $surat): array
    {
        $y = now()->year;
        $map = [];
        foreach ([$y - 1, $y, $y + 1] as $t) {
            $map[$t] = $this->svc->nomorBerikutnya($t);
        }

        return [
            'surat'       => $surat,
            'klasifikasi' => KlasifikasiSurat::orderBy('kode_indeks')->get(),
            'pegawai'     => Pegawai::where('aktif', true)->orderBy('nama_lengkap')->get(),
            'kop'         => PengaturanSekolah::kop(),
            'nomorMap'    => $map,
        ];
    }

    private function valid(Request $r): array
    {
        return $r->validate([
            'id_klasifikasi'    => 'required|exists:klasifikasi_surat,id_klasifikasi',
            'id_penandatangan'  => 'required|exists:pegawai,id_pegawai',
            'tgl_surat'         => 'required|date',
            'perihal'           => 'required|string|max:255',
            'lampiran'          => 'nullable|string|max:255',
            'tujuan_surat'      => 'required|string',
            'paragraf_pembuka'  => 'nullable|string',
            'paragraf_penutup'  => 'nullable|string',
            'hari_tgl_kegiatan' => 'required_with:ada_kegiatan|nullable|string|max:255',
            'waktu_kegiatan'    => 'required_with:ada_kegiatan|nullable|string|max:255',
            'tempat_kegiatan'   => 'required_with:ada_kegiatan|nullable|string|max:255',
        ]);
    }

    private function simpan(SuratKeluar $s, array $d, Request $r): SuratKeluar
    {
        DB::transaction(function () use ($s, $d, $r) {
            $s->fill([
                'id_klasifikasi'   => $d['id_klasifikasi'],
                'id_penandatangan' => $d['id_penandatangan'],
                'tgl_surat'        => $d['tgl_surat'],
                'perihal'          => $d['perihal'],
                'lampiran'         => $d['lampiran'] ?: '-',
                'tujuan_surat'     => $d['tujuan_surat'],
                'paragraf_pembuka' => Html::bersih($d['paragraf_pembuka'] ?? '') ?: null,
                'paragraf_penutup' => Html::bersih($d['paragraf_penutup'] ?? '') ?: null,
            ]);

            if (!$s->exists) {
                $s->id_pembuat      = $r->user()->id_user;
                $s->status_surat    = 'Draft';
                $s->kode_verifikasi = $this->svc->kodeUnik();
            }
            $this->svc->isiTurunan($s);
            $s->save();

            if ($r->boolean('ada_kegiatan')) {
                DetailKegiatan::updateOrCreate(['id_surat' => $s->id_surat], [
                    'hari_tgl_kegiatan' => $d['hari_tgl_kegiatan'],
                    'waktu_kegiatan'    => $d['waktu_kegiatan'],
                    'tempat_kegiatan'   => $d['tempat_kegiatan'],
                ]);
            } else {
                DetailKegiatan::where('id_surat', $s->id_surat)->delete();
            }
        });

        return $s;
    }

    private function lanjut(Request $r, SuratKeluar $s)
    {
        if ($r->input('aksi') !== 'final') {
            return redirect()->route('surat.edit', $s)->with('ok', 'Draft tersimpan.');
        }

        try {
            $this->svc->finalisasi($s, $r->user(), $r->catatan_acc);
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('surat.edit', $s)->withErrors(['pdf' => 'Gagal membuat PDF: ' . $e->getMessage()]);
        }

        return redirect()->route('surat.show', $s)->with('ok', 'Surat final dengan nomor ' . $s->nomor_lengkap);
    }
}