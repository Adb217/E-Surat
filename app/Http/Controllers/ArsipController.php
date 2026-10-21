<?php

namespace App\Http\Controllers;

use App\Models\{KlasifikasiSurat, SuratKeluar, SuratMasuk};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArsipController extends Controller
{
    public function index(Request $r)
    {
        $arah   = in_array($r->arah, ['keluar', 'masuk'], true) ? $r->arah : null;
        $status = in_array($r->status, ['Draft', 'Final', 'Dibatalkan'], true) ? $r->status : null;
        if ($arah === 'masuk') {
            $status = null;
        }
        $bulan = $r->filled('bulan') ? (int) $r->bulan : null;
        $tahun = $r->filled('tahun') ? (int) $r->tahun : null;
        $jenis = $r->filled('jenis') ? (int) $r->jenis : null;
        $kata  = mb_strtolower(trim((string) $r->q));

        $cari = function ($b, array $cols) use ($kata) {
            if ($kata === '') {
                return $b;
            }
            return $b->where(function ($w) use ($cols, $kata) {
                foreach ($cols as $c) {
                    $w->orWhereRaw("LOWER($c) LIKE ?", ['%' . $kata . '%']);
                }
            });
        };

        $qKeluar = fn () => $cari(
            DB::table('surat_keluar as s')
                ->leftJoin('klasifikasi_surat as k', 'k.id_klasifikasi', '=', 's.id_klasifikasi')
                ->when($bulan, fn ($b) => $b->whereMonth('s.tgl_surat', $bulan))
                ->when($tahun, fn ($b) => $b->whereYear('s.tgl_surat', $tahun))
                ->when($jenis, fn ($b) => $b->where('s.id_klasifikasi', $jenis)),
            ['s.perihal', 's.nomor_lengkap', 's.kode_verifikasi', 's.tujuan_surat']
        );

        $qMasuk = fn () => $cari(
            DB::table('surat_masuk as s')
                ->leftJoin('klasifikasi_surat as k', 'k.id_klasifikasi', '=', 's.id_klasifikasi')
                ->when($bulan, fn ($b) => $b->whereMonth('s.tgl_diterima', $bulan))
                ->when($tahun, fn ($b) => $b->whereYear('s.tgl_diterima', $tahun))
                ->when($jenis, fn ($b) => $b->where('s.id_klasifikasi', $jenis)),
            ['s.perihal', 's.nomor_surat_asal', 's.kode_unik', 's.pengirim']
        );

        $kolomK = "'keluar' as arah, s.id_surat as id, s.tgl_surat as tanggal, s.kode_verifikasi as kode, "
            . "k.kode_indeks as kls_kode, k.keterangan as kls_ket, s.status_surat as status";
        $kolomM = "'masuk' as arah, s.id_masuk as id, s.tgl_diterima as tanggal, s.kode_unik as kode, "
            . "k.kode_indeks as kls_kode, k.keterangan as kls_ket, 'Diterima' as status";

        // daftar gabungan keluar + masuk
        $bagian = [];
        if ($arah !== 'masuk') {
            $bagian[] = $qKeluar()->when($status, fn ($b) => $b->where('s.status_surat', $status))->selectRaw($kolomK);
        }
        if ($arah !== 'keluar' && !$status) {
            $bagian[] = $qMasuk()->selectRaw($kolomM);
        }
        $dasar = array_shift($bagian);
        foreach ($bagian as $b) {
            $dasar->unionAll($b);
        }

        $items = DB::query()->fromSub($dasar, 'u')
            ->orderByDesc('tanggal')->orderByDesc('id')
            ->paginate(15)->withQueryString();

        // hitungan ikut filter bulan/tahun/jenis/pencarian
        $nKeluar = $qKeluar()->where('s.status_surat', 'Final')->count();
        $nDraft  = $qKeluar()->where('s.status_surat', 'Draft')->count();
        $nMasuk  = $qMasuk()->count();

        // rekap per bulan untuk satu tahun
        $ry = $tahun ?? now()->year;
        $rk = SuratKeluar::where('status_surat', 'Final')->whereYear('tgl_surat', $ry)->pluck('tgl_surat')->countBy(fn ($d) => $d->month);
        $rm = SuratMasuk::whereYear('tgl_diterima', $ry)->pluck('tgl_diterima')->countBy(fn ($d) => $d->month);

        $tahunList = SuratKeluar::pluck('tgl_surat')->map->year
            ->merge(SuratMasuk::pluck('tgl_diterima')->map->year)
            ->push(now()->year)->unique()->sortDesc()->values();

        return view('arsip.index', [
            'items'       => $items,
            'klasifikasi' => KlasifikasiSurat::orderBy('kode_indeks')->get(),
            'tahunList'   => $tahunList,
            'nKeluar' => $nKeluar, 'nMasuk' => $nMasuk, 'nDraft' => $nDraft,
            'bulan' => $bulan, 'tahun' => $tahun, 'ry' => $ry, 'rk' => $rk, 'rm' => $rm,
        ]);
    }
}