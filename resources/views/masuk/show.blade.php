@extends('layouts.app')
@section('title', 'Detail Surat Masuk')

@section('content')
<div class="mb-6 flex flex-wrap items-start justify-between gap-3">
    <div class="min-w-0">
        <h1 class="text-xl font-extrabold tracking-tight sm:text-2xl">{{ $masuk->perihal }}</h1>
        <p class="mt-1 text-xs text-gray-500">Kode: <span class="font-mono font-semibold text-gray-800">{{ $masuk->kode_unik }}</span></p>
    </div>
    <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-600">Surat masuk</span>
</div>

<div class="max-w-3xl space-y-5">
    <section class="card text-sm">
        <dl class="grid grid-cols-[130px_1fr] gap-x-3 gap-y-2">
            <dt class="text-gray-500">Tanggal diterima</dt><dd>{{ $masuk->tgl_diterima->locale('id')->translatedFormat('d F Y') }}</dd>
            <dt class="text-gray-500">Tanggal surat</dt><dd>{{ $masuk->tgl_surat?->locale('id')->translatedFormat('d F Y') ?? '-' }}</dd>
            <dt class="text-gray-500">Nomor asal</dt><dd>{{ $masuk->nomor_surat_asal ?: '-' }}</dd>
            <dt class="text-gray-500">Pengirim</dt><dd>{{ $masuk->pengirim }}</dd>
            <dt class="text-gray-500">Jenis surat</dt><dd>{{ $masuk->klasifikasi ? $masuk->klasifikasi->kode_indeks . ' - ' . $masuk->klasifikasi->keterangan : '-' }}</dd>
            <dt class="text-gray-500">Dicatat oleh</dt><dd>{{ $masuk->pencatat->nama_lengkap }}</dd>
            @if ($masuk->catatan)
                <dt class="text-gray-500">Catatan</dt><dd class="whitespace-pre-line">{{ $masuk->catatan }}</dd>
            @endif
        </dl>
    </section>

    <section class="card flex flex-wrap gap-3">
        @if ($masuk->file_scan)
            <a href="{{ route('masuk.berkas', $masuk) }}" target="_blank" class="btn-primary">Buka berkas</a>
            <a href="{{ route('masuk.berkas', [$masuk, 'unduh' => 1]) }}" class="btn-ghost">Unduh</a>
        @else
            <span class="text-sm text-gray-400">Tidak ada berkas scan.</span>
        @endif
        <a href="{{ route('masuk.edit', $masuk) }}" class="btn-ghost">Edit</a>
        <form method="POST" action="{{ route('masuk.destroy', $masuk) }}" onsubmit="return confirm('Hapus surat masuk ini?')">
            @csrf @method('DELETE')
            <button class="btn-ghost !text-red-600">Hapus</button>
        </form>
    </section>
</div>
@endsection