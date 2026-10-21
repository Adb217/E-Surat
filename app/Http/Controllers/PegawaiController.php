<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PegawaiController extends Controller
{
    public function index()
    {
        return view('pegawai.index', ['items' => Pegawai::orderByDesc('aktif')->orderBy('nama_lengkap')->get()]);
    }

    public function store(Request $r)
    {
        $d = $this->valid($r);
        if ($r->hasFile('ttd')) {
            $d['file_ttd_stempel'] = $r->file('ttd')->store('ttd', 'public');
        }
        Pegawai::create($d);

        return redirect()->route('pegawai.index')->with('ok', 'Penandatangan ditambahkan.');
    }

    public function edit(Pegawai $pegawai)
    {
        return view('pegawai.edit', ['p' => $pegawai]);
    }

    public function update(Request $r, Pegawai $pegawai)
    {
        $d = $this->valid($r);
        if ($r->hasFile('ttd')) {
            if ($pegawai->file_ttd_stempel) {
                Storage::disk('public')->delete($pegawai->file_ttd_stempel);
            }
            $d['file_ttd_stempel'] = $r->file('ttd')->store('ttd', 'public');
        }
        $pegawai->update($d);

        return redirect()->route('pegawai.index')->with('ok', 'Data penandatangan diperbarui.');
    }

    public function destroy(Pegawai $pegawai)
    {
        if ($pegawai->surat()->exists()) {
            return back()->withErrors(['x' => 'Sudah dipakai di surat. Nonaktifkan saja lewat Edit.']);
        }
        if ($pegawai->file_ttd_stempel) {
            Storage::disk('public')->delete($pegawai->file_ttd_stempel);
        }
        $pegawai->delete();

        return back()->with('ok', 'Penandatangan dihapus.');
    }

    private function valid(Request $r): array
    {
        $d = $r->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nip'          => 'nullable|string|max:50',
            'jabatan'      => 'required|string|max:255',
            'ttd'          => 'nullable|image|max:2048',
        ]);
        unset($d['ttd']);
        $d['aktif'] = $r->boolean('aktif');

        return $d;
    }
}