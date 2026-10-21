<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use Illuminate\Http\Request;

class VerifikasiController extends Controller
{
    public function form()
    {
        return view('verifikasi-cari');
    }

    public function cari(Request $r)
    {
        $r->validate(['kode' => 'required|string|max:40']);
        $kode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $r->kode));

        return redirect()->route('verifikasi', $kode);
    }

    public function show(string $kode)
    {
        return view('verifikasi', ['surat' => $this->cariSurat($kode), 'kode' => $kode, 'hasil' => session('hasil')]);
    }

    public function cek(Request $r, string $kode)
    {
        $surat = $this->cariSurat($kode);
        $r->validate(['pdf' => 'required|file|mimes:pdf|max:10240']);
        abort_unless($surat, 404);

        $cocok = hash_equals($surat->hash_dokumen, hash_file('sha256', $r->file('pdf')->getRealPath()));

        return redirect()->route('verifikasi', $kode)->with('hasil', $cocok ? 'cocok' : 'beda');
    }

    private function cariSurat(string $kode): ?SuratKeluar
    {
        return SuratKeluar::with(['klasifikasi', 'penandatangan'])
            ->where('kode_verifikasi', strtoupper($kode))
            ->whereIn('status_surat', ['Final', 'Dibatalkan'])
            ->first();
    }
}