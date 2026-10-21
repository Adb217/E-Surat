@extends('layouts.app')
@section('title', 'Kode Klasifikasi')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold tracking-tight">Kode Klasifikasi Surat</h1>
    <p class="mt-1 text-sm text-gray-500">Kode ini jadi bagian nomor surat, contoh: 571/SMK-Kosgoro/<b>H</b>/IX/2026.</p>
</div>

<form method="POST" action="{{ route('klasifikasi.store') }}" class="card mb-5 flex flex-wrap items-end gap-3">
    @csrf
    <div>
        <label class="lbl">Kode</label>
        <input name="kode_indeks" class="inp !w-24 font-mono uppercase" maxlength="10" placeholder="H" required>
    </div>
    <div class="min-w-56 flex-1">
        <label class="lbl">Keterangan</label>
        <input name="keterangan" class="inp" placeholder="Humas & Undangan" required>
    </div>
    <button class="btn-primary">Tambah</button>
</form>

<section class="card space-y-3">
    @forelse ($items as $k)
        <div class="flex flex-wrap items-center gap-2 border-b border-gray-100 pb-3 last:border-0 last:pb-0">
            <form method="POST" action="{{ route('klasifikasi.update', $k) }}" class="flex min-w-72 flex-1 flex-wrap items-center gap-2">
                @csrf @method('PUT')
                <input name="kode_indeks" value="{{ $k->kode_indeks }}" class="inp !w-24 font-mono uppercase" maxlength="10">
                <input name="keterangan" value="{{ $k->keterangan }}" class="inp min-w-40 flex-1">
                <button class="btn-ghost !px-3 !py-2">Simpan</button>
            </form>
            <span class="text-xs text-gray-400">{{ $k->surat_count }} surat</span>
            <form method="POST" action="{{ route('klasifikasi.destroy', $k) }}" onsubmit="return confirm('Hapus kode ini?')">
                @csrf @method('DELETE')
                <button class="rounded-xl px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">Hapus</button>
            </form>
        </div>
    @empty
        <p class="py-6 text-center text-sm text-gray-400">Belum ada kode klasifikasi.</p>
    @endforelse
</section>
@endsection