<?php

namespace App\Http\Controllers;

use App\Models\{KlasifikasiSurat, SuratMasuk};
use App\Services\SuratService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuratMasukController extends Controller
{
    public function __construct(private SuratService $svc) {}

    public function create()
    {
        return view('masuk.form', $this->data(new SuratMasuk));
    }

    public function store(Request $r)
    {
        $d = $this->valid($r);

        $m = new SuratMasuk($d);
        $m->kode_unik   = $this->svc->kodeUnik();
        $m->id_pencatat = $r->user()->id_user;
        if ($r->hasFile('berkas')) {
            $m->file_scan = $r->file('berkas')->store('surat-masuk', 'local');
        }
        $m->save();

        $this->svc->log($r->user(), null, 'masuk_catat', $m->kode_unik);

        return redirect()->route('masuk.show', $m)->with('ok', 'Surat masuk tercatat dengan kode ' . $m->kode_unik);
    }

    public function show(SuratMasuk $masuk)
    {
        return view('masuk.show', ['masuk' => $masuk->load(['klasifikasi', 'pencatat'])]);
    }

    public function edit(SuratMasuk $masuk)
    {
        return view('masuk.form', $this->data($masuk));
    }

    public function update(Request $r, SuratMasuk $masuk)
    {
        $masuk->fill($this->valid($r));
        if ($r->hasFile('berkas')) {
            if ($masuk->file_scan) {
                Storage::disk('local')->delete($masuk->file_scan);
            }
            $masuk->file_scan = $r->file('berkas')->store('surat-masuk', 'local');
        }
        $masuk->save();

        return redirect()->route('masuk.show', $masuk)->with('ok', 'Surat masuk diperbarui.');
    }

    public function destroy(Request $r, SuratMasuk $masuk)
    {
        if ($masuk->file_scan) {
            Storage::disk('local')->delete($masuk->file_scan);
        }
        $this->svc->log($r->user(), null, 'masuk_hapus', $masuk->kode_unik);
        $masuk->delete();

        return redirect()->route('surat.index')->with('ok', 'Surat masuk dihapus.');
    }

    public function berkas(Request $r, SuratMasuk $masuk)
    {
        abort_unless($masuk->file_scan && Storage::disk('local')->exists($masuk->file_scan), 404);

        $nama = 'surat-masuk-' . $masuk->kode_unik . '.' . pathinfo($masuk->file_scan, PATHINFO_EXTENSION);

        return $r->boolean('unduh')
            ? Storage::disk('local')->download($masuk->file_scan, $nama)
            : Storage::disk('local')->response($masuk->file_scan, $nama);
    }

    private function data(SuratMasuk $m): array
    {
        return ['m' => $m, 'klasifikasi' => KlasifikasiSurat::orderBy('kode_indeks')->get()];
    }

    private function valid(Request $r): array
    {
        $d = $r->validate([
            'tgl_diterima'     => 'required|date',
            'tgl_surat'        => 'nullable|date',
            'nomor_surat_asal' => 'nullable|string|max:255',
            'pengirim'         => 'required|string|max:255',
            'perihal'          => 'required|string|max:255',
            'id_klasifikasi'   => 'nullable|exists:klasifikasi_surat,id_klasifikasi',
            'catatan'          => 'nullable|string',
            'berkas'           => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:10240',
        ]);
        unset($d['berkas']);

        return $d;
    }
}