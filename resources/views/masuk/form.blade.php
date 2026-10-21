@extends('layouts.app')
@section('title', $m->exists ? 'Edit Surat Masuk' : 'Catat Surat Masuk')

@section('content')
<h1 class="mb-6 text-2xl font-extrabold tracking-tight">{{ $m->exists ? 'Edit Surat Masuk' : 'Catat Surat Masuk' }}</h1>

<form method="POST" enctype="multipart/form-data" class="card max-w-3xl space-y-4"
      action="{{ $m->exists ? route('masuk.update', $m) : route('masuk.store') }}">
    @csrf
    @if ($m->exists) @method('PUT') @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="lbl">Tanggal diterima *</label>
            <input type="date" name="tgl_diterima" class="inp" value="{{ old('tgl_diterima', $m->tgl_diterima?->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
        </div>
        <div>
            <label class="lbl">Tanggal pada surat</label>
            <input type="date" name="tgl_surat" class="inp" value="{{ old('tgl_surat', $m->tgl_surat?->format('Y-m-d')) }}">
        </div>
        <div>
            <label class="lbl">Nomor surat asal</label>
            <input name="nomor_surat_asal" class="inp" value="{{ old('nomor_surat_asal', $m->nomor_surat_asal) }}">
        </div>
        <div>
            <label class="lbl">Pengirim *</label>
            <input name="pengirim" class="inp" value="{{ old('pengirim', $m->pengirim) }}" placeholder="Dinas Pendidikan Jabar">
        </div>
        <div class="sm:col-span-2">
            <label class="lbl">Perihal *</label>
            <input name="perihal" class="inp" value="{{ old('perihal', $m->perihal) }}">
        </div>
        <div>
            <label class="lbl">Jenis surat</label>
            <select name="id_klasifikasi" class="inp">
                <option value="">- belum ditentukan -</option>
                @foreach ($klasifikasi as $k)
                    <option value="{{ $k->id_klasifikasi }}" @selected(old('id_klasifikasi', $m->id_klasifikasi) == $k->id_klasifikasi)>{{ $k->kode_indeks }} - {{ $k->keterangan }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="lbl">Berkas scan (opsional)</label>
            <input type="file" name="berkas" class="inp !py-2 text-xs file:mr-3 file:rounded-lg file:border-0 file:bg-orange-50 file:px-3 file:py-1.5 file:font-semibold file:text-orange-700">
            <p class="mt-1 text-[11px] text-gray-400">PDF/gambar/Word, maks 10 MB.{{ $m->file_scan ? ' Mengunggah baru akan mengganti berkas lama.' : '' }}</p>
        </div>
        <div class="sm:col-span-2">
            <label class="lbl">Catatan</label>
            <textarea name="catatan" rows="3" class="inp">{{ old('catatan', $m->catatan) }}</textarea>
        </div>
    </div>

    <div class="flex gap-3">
        <button class="btn-primary">Simpan</button>
        <a href="{{ $m->exists ? route('masuk.show', $m) : route('surat.index') }}" class="btn-ghost">Batal</a>
    </div>
</form>
@endsection