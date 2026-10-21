<?php

namespace App\Http\Controllers;

use App\Models\KlasifikasiSurat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KlasifikasiController extends Controller
{
    public function index()
    {
        return view('klasifikasi.index', [
            'items' => KlasifikasiSurat::withCount('surat')->orderBy('kode_indeks')->get(),
        ]);
    }

    public function store(Request $r)
    {
        $r->merge(['kode_indeks' => strtoupper(trim((string) $r->kode_indeks))]);
        $d = $r->validate([
            'kode_indeks' => 'required|max:10|unique:klasifikasi_surat,kode_indeks',
            'keterangan'  => 'required|max:255',
        ]);
        KlasifikasiSurat::create($d);

        return back()->with('ok', 'Klasifikasi ditambahkan.');
    }

    public function update(Request $r, KlasifikasiSurat $klasifikasi)
    {
        $r->merge(['kode_indeks' => strtoupper(trim((string) $r->kode_indeks))]);
        $d = $r->validate([
            'kode_indeks' => ['required', 'max:10',
                Rule::unique('klasifikasi_surat', 'kode_indeks')->ignore($klasifikasi->id_klasifikasi, 'id_klasifikasi')],
            'keterangan'  => 'required|max:255',
        ]);
        $klasifikasi->update($d);

        return back()->with('ok', 'Klasifikasi diperbarui. Surat yang sudah final tidak berubah nomornya.');
    }

    public function destroy(KlasifikasiSurat $klasifikasi)
    {
        if ($klasifikasi->surat()->exists()) {
            return back()->withErrors(['x' => 'Kode ini sudah dipakai di surat, tidak bisa dihapus.']);
        }
        $klasifikasi->delete();

        return back()->with('ok', 'Klasifikasi dihapus.');
    }
}