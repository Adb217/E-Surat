@extends('layouts.app')
@section('title', 'Penandatangan')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold tracking-tight">Penandatangan</h1>
    <p class="mt-1 text-sm text-gray-500">Gambar tanda tangan &amp; stempel di sini yang ditempel otomatis ke PDF final.</p>
</div>

<form method="POST" action="{{ route('pegawai.store') }}" enctype="multipart/form-data" class="card mb-5">
    @csrf
    <h2 class="mb-4 text-sm font-bold">Tambah penandatangan</h2>
    @include('pegawai._form', ['p' => null])
    <button class="btn-primary mt-5">Simpan</button>
</form>

<section class="card !p-0 divide-y divide-gray-100">
    @forelse ($items as $p)
        <div class="flex flex-wrap items-center gap-4 px-5 py-4">
            <div class="flex h-14 w-20 items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                @if ($p->file_ttd_stempel)
                    <img src="{{ asset('storage/' . $p->file_ttd_stempel) }}" class="max-h-full max-w-full">
                @else
                    <span class="text-[10px] text-gray-400">belum ada</span>
                @endif
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-sm font-bold">{{ $p->nama_lengkap }}</div>
                <div class="text-xs text-gray-500">{{ $p->jabatan }} @if ($p->nip) · NIP {{ $p->nip }} @endif</div>
            </div>
            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $p->aktif ? 'bg-orange-50 text-orange-700' : 'bg-gray-100 text-gray-500' }}">{{ $p->aktif ? 'Aktif' : 'Nonaktif' }}</span>
            <a href="{{ route('pegawai.edit', $p) }}" class="text-xs font-semibold text-orange-700 hover:underline">Edit</a>
            <form method="POST" action="{{ route('pegawai.destroy', $p) }}" onsubmit="return confirm('Hapus penandatangan ini?')">
                @csrf @method('DELETE')
                <button class="text-xs font-semibold text-red-600 hover:underline">Hapus</button>
            </form>
        </div>
    @empty
        <p class="px-5 py-10 text-center text-sm text-gray-400">Belum ada penandatangan.</p>
    @endforelse
</section>
@endsection