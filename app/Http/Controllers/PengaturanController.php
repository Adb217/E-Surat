<?php

namespace App\Http\Controllers;

use App\Models\PengaturanSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    const KEYS = [
        'nama_yayasan' => 'Nama yayasan',
        'nama_program' => 'Nama program',
        'nama_sekolah' => 'Nama sekolah',
        'akreditasi'   => 'Akreditasi',
        'nss'          => 'NSS',
        'npsn'         => 'NPSN',
        'alamat'       => 'Alamat',
        'telepon'      => 'Telepon',
        'website'      => 'Website',
        'email'        => 'Email',
    ];

    public function edit()
    {
        return view('pengaturan.edit', ['kop' => PengaturanSekolah::kop(), 'keys' => self::KEYS]);
    }

    public function update(Request $r)
    {
        $rules = ['nomor_awal' => 'required|integer|min:1', 'logo' => 'nullable|image|max:2048'];
        foreach (array_keys(self::KEYS) as $k) {
            $rules[$k] = 'nullable|string|max:500';
        }
        $d = $r->validate($rules);

        foreach (array_keys(self::KEYS) as $k) {
            PengaturanSekolah::updateOrCreate(['kunci' => $k], ['nilai' => $d[$k] ?? null]);
        }
        PengaturanSekolah::updateOrCreate(['kunci' => 'nomor_awal'], ['nilai' => (string) $d['nomor_awal']]);

        if ($r->hasFile('logo')) {
            $lama = PengaturanSekolah::ambil('logo');
            if ($lama) {
                Storage::disk('public')->delete($lama);
            }
            PengaturanSekolah::updateOrCreate(['kunci' => 'logo'], ['nilai' => $r->file('logo')->store('logo', 'public')]);
        }

        return back()->with('ok', 'Pengaturan disimpan.');
    }
}