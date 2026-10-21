<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use App\Services\SuratService;

class DashboardController extends Controller
{
    public function __invoke(SuratService $svc)
    {
        $tahun = now()->year;

        return view('dashboard', [
            'tahun'    => $tahun,
            'final'    => SuratKeluar::where('tahun', $tahun)->where('status_surat', 'Final')->count(),
            'draft'    => SuratKeluar::where('status_surat', 'Draft')->count(),
            'batal'    => SuratKeluar::where('tahun', $tahun)->where('status_surat', 'Dibatalkan')->count(),
            'berikut'  => $svc->nomorBerikutnya($tahun),
            'terbaru'  => SuratKeluar::with('klasifikasi')->latest('id_surat')->limit(6)->get(),
        ]);
    }
}   