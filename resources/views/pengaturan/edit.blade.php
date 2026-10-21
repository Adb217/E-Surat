@extends('layouts.app')
@section('title', 'Pengaturan Sistem')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold tracking-tight">Pengaturan Sistem</h1>
    <p class="mt-1 text-sm text-gray-500">Data kop surat dan penomoran.</p>
</div>

<form method="POST" action="{{ route('pengaturan.update') }}" enctype="multipart/form-data" class="space-y-5">
    @csrf @method('PUT')

    <section class="card space-y-4">
        <h2 class="text-sm font-bold">Kop surat</h2>
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($keys as $key => $label)
                <div class="{{ in_array($key, ['alamat', 'nama_sekolah']) ? 'sm:col-span-2' : '' }}">
                    <label class="lbl">{{ $label }}</label>
                    <input name="{{ $key }}" class="inp" value="{{ old($key, $kop[$key] ?? '') }}">
                </div>
            @endforeach
        </div>
        <div>
            <label class="lbl">Logo sekolah</label>
            <input type="file" name="logo" accept="image/*" class="inp !py-2 text-xs file:mr-3 file:rounded-lg file:border-0 file:bg-orange-50 file:px-3 file:py-1.5 file:font-semibold file:text-orange-700">
            @if ($kop['logo_url'])
                <img src="{{ $kop['logo_url'] }}" class="mt-3 h-20 rounded-lg border border-gray-200 p-1">
            @endif
        </div>
    </section>

    <section class="card space-y-3">
        <h2 class="text-sm font-bold">Penomoran</h2>
        <div class="max-w-xs">
            <label class="lbl">Nomor urut awal</label>
            <input type="number" min="1" name="nomor_awal" class="inp" value="{{ old('nomor_awal', $kop['nomor_awal'] ?? 1) }}">
        </div>
        <p class="text-xs text-gray-500">Hanya dipakai untuk surat bernomor pertama di sistem ini (misal lanjut dari nomor 571). Setiap pergantian tahun, nomor mulai lagi dari 1.</p>
    </section>

    <button class="btn-primary">Simpan pengaturan</button>
</form>
@endsection