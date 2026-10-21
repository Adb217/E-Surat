@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight">Halo, {{ auth()->user()->nama_lengkap }}</h1>
        <p class="mt-1 text-sm text-gray-500">Ringkasan surat keluar tahun {{ $tahun }}.</p>
    </div>
    <a href="{{ route('surat.create') }}" class="btn-primary">+ Buat Surat Baru</a>
</div>

<div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
    @foreach ([['Surat final', $final, true], ['Draft', $draft, false], ['Dibatalkan', $batal, false], ['Nomor berikutnya', $berikut, false]] as [$lbl, $val, $utama])
        <div class="card {{ $utama ? 'bg-gradient-to-br from-orange-500 to-orange-600 !border-orange-500 text-white' : '' }}">
            <div class="text-xs font-semibold {{ $utama ? 'text-orange-100' : 'text-gray-500' }}">{{ $lbl }}</div>
            <div class="mt-2 text-3xl font-extrabold">{{ $val }}</div>
        </div>
    @endforeach
</div>

<section class="card !p-0">
    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
        <h2 class="text-sm font-bold">Surat terbaru</h2>
        <a href="{{ route('surat.index') }}" class="text-xs font-semibold text-orange-700 hover:underline">Lihat semua</a>
    </div>
    @forelse ($terbaru as $s)
        <a href="{{ route('surat.show', $s) }}" class="flex items-center justify-between gap-3 border-b border-gray-50 px-5 py-3 last:border-0 hover:bg-gray-50">
            <div class="min-w-0">
                <div class="truncate text-sm font-semibold">{{ $s->perihal }}</div>
                <div class="truncate font-mono text-xs text-gray-500">{{ $s->nomor_tampil }}</div>
            </div>
            @include('partials.badge', ['status' => $s->status_surat])
        </a>
    @empty
        <p class="px-5 py-10 text-center text-sm text-gray-400">Belum ada surat. Mulai dengan tombol "Buat Surat Baru".</p>
    @endforelse
</section>
@endsection