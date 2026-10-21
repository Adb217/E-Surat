@extends('layouts.app')
@section('title', 'Arsip Surat')

@section('content')
@php
    $namaBulan = fn ($m) => \Carbon\Carbon::create(2000, $m, 1)->locale('id')->translatedFormat('F');
    $periode = ($bulan || $tahun) ? trim(($bulan ? $namaBulan($bulan) : '') . ' ' . ($tahun ?: '')) : 'Semua periode';
@endphp

<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight">Arsip Surat</h1>
        <p class="mt-1 text-sm text-gray-500">Daftar surat keluar dan masuk. Isi surat dibuka lewat halaman detail.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('masuk.create') }}" class="btn-ghost">+ Catat Surat Masuk</a>
        <a href="{{ route('surat.create') }}" class="btn-primary">+ Buat Surat Keluar</a>
    </div>
</div>

{{-- hitungan --}}
<div class="mb-5 grid grid-cols-2 gap-4 lg:grid-cols-4">
    <div class="card bg-gradient-to-br from-orange-500 to-orange-600 !border-orange-500 text-white">
        <div class="text-xs font-semibold text-orange-100">Surat keluar</div>
        <div class="mt-2 text-3xl font-extrabold">{{ $nKeluar }}</div>
    </div>
    <div class="card">
        <div class="text-xs font-semibold text-gray-500">Surat masuk</div>
        <div class="mt-2 text-3xl font-extrabold">{{ $nMasuk }}</div>
    </div>
    <div class="card">
        <div class="text-xs font-semibold text-gray-500">Draft</div>
        <div class="mt-2 text-3xl font-extrabold">{{ $nDraft }}</div>
    </div>
    <div class="card">
        <div class="text-xs font-semibold text-gray-500">Periode</div>
        <div class="mt-2 text-lg font-extrabold leading-snug">{{ $periode }}</div>
    </div>
</div>

{{-- filter --}}
<form method="GET" action="{{ route('surat.index') }}" class="card mb-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
    <input name="q" value="{{ request('q') }}" placeholder="Cari kode / nomor / perihal" class="inp sm:col-span-2">
    <select name="arah" class="inp">
        <option value="">Keluar &amp; masuk</option>
        <option value="keluar" @selected(request('arah') === 'keluar')>Surat keluar</option>
        <option value="masuk" @selected(request('arah') === 'masuk')>Surat masuk</option>
    </select>
    <select name="bulan" class="inp">
        <option value="">Semua bulan</option>
        @foreach (range(1, 12) as $m)
            <option value="{{ $m }}" @selected($bulan === $m)>{{ $namaBulan($m) }}</option>
        @endforeach
    </select>
    <select name="tahun" class="inp">
        <option value="">Semua tahun</option>
        @foreach ($tahunList as $t)
            <option value="{{ $t }}" @selected($tahun === $t)>{{ $t }}</option>
        @endforeach
    </select>
    <select name="jenis" class="inp">
        <option value="">Semua jenis</option>
        @foreach ($klasifikasi as $k)
            <option value="{{ $k->id_klasifikasi }}" @selected(request('jenis') == $k->id_klasifikasi)>{{ $k->kode_indeks }} - {{ $k->keterangan }}</option>
        @endforeach
    </select>
    <select name="status" class="inp">
        <option value="">Semua status</option>
        @foreach (['Draft', 'Final', 'Dibatalkan'] as $st)
            <option value="{{ $st }}" @selected(request('status') === $st)>{{ $st }}</option>
        @endforeach
    </select>
    <div class="flex gap-2 sm:col-span-2 lg:col-span-5 lg:justify-end">
        <a href="{{ route('surat.index') }}" class="btn-ghost">Reset</a>
        <button class="btn-primary">Terapkan</button>
    </div>
</form>

{{-- rekap per bulan --}}
<section class="card mb-5">
    <h2 class="mb-3 text-sm font-bold">Rekap {{ $ry }} <span class="font-normal text-gray-400">· K = keluar, M = masuk (klik untuk filter bulan)</span></h2>
    <div class="grid grid-cols-3 gap-2 sm:grid-cols-4 lg:grid-cols-6">
        @foreach (range(1, 12) as $m)
            <a href="{{ route('surat.index', ['tahun' => $ry, 'bulan' => $m]) }}"
               class="rounded-xl border px-3 py-2 text-xs transition hover:border-orange-300 {{ $bulan === $m ? 'border-orange-400 bg-orange-50' : 'border-gray-200' }}">
                <div class="font-bold text-gray-700">{{ $namaBulan($m) }}</div>
                <div class="mt-0.5 text-gray-500">K <b class="text-orange-700">{{ $rk[$m] ?? 0 }}</b> · M <b class="text-gray-800">{{ $rm[$m] ?? 0 }}</b></div>
            </a>
        @endforeach
    </div>
</section>

{{-- daftar --}}
<section class="card !p-0 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3">Kode</th>
                    <th class="px-5 py-3">Jenis surat</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($items as $it)
                    <tr class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-5 py-3 text-gray-700">{{ \Carbon\Carbon::parse($it->tanggal)->locale('id')->translatedFormat('d M Y') }}</td>
                        <td class="whitespace-nowrap px-5 py-3">
                            <span class="font-mono font-bold tracking-wide">{{ $it->kode ?: '-' }}</span>
                            <span class="ml-1.5 rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $it->arah === 'keluar' ? 'bg-orange-50 text-orange-700' : 'bg-gray-100 text-gray-600' }}">{{ $it->arah === 'keluar' ? 'Keluar' : 'Masuk' }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-700">{{ $it->kls_kode ? $it->kls_kode . ' · ' . $it->kls_ket : '-' }}</td>
                        <td class="px-5 py-3">@include('partials.badge', ['status' => $it->status])</td>
                        <td class="whitespace-nowrap px-5 py-3 text-right">
                            <a href="{{ $it->arah === 'keluar' ? route('surat.show', $it->id) : route('masuk.show', $it->id) }}"
                               class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:border-orange-300 hover:text-orange-700">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-12 text-center text-gray-400">Tidak ada surat yang cocok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<div class="mt-4">{{ $items->links() }}</div>
@endsection