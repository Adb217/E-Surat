@extends('layouts.app')
@section('title', 'Detail Surat')

@section('content')
@php $d = $surat->detail; @endphp

<div class="mb-6 flex flex-wrap items-start justify-between gap-3">
    <div class="min-w-0">
        <h1 class="text-xl font-extrabold tracking-tight sm:text-2xl">{{ $surat->perihal }}</h1>
        <p class="mt-1 font-mono text-sm text-gray-500">{{ $surat->nomor_tampil }}</p>
        <p class="mt-1 text-xs text-gray-500">Kode: <span class="font-mono font-semibold text-gray-800">{{ $surat->kode_verifikasi }}</span></p>
    </div>
    @include('partials.badge', ['status' => $surat->status_surat])
</div>

<div class="gap-6 lg:grid lg:grid-cols-[minmax(0,1fr)_minmax(0,1.1fr)]">
    <div class="space-y-5">
        <section class="card space-y-3 text-sm">
            <h2 class="text-sm font-bold">Ringkasan</h2>
            <dl class="grid grid-cols-[110px_1fr] gap-x-3 gap-y-2">
                <dt class="text-gray-500">Tanggal</dt><dd>{{ $surat->tgl_surat->locale('id')->translatedFormat('d F Y') }}</dd>
                <dt class="text-gray-500">Klasifikasi</dt><dd>{{ $surat->klasifikasi->kode_indeks }} - {{ $surat->klasifikasi->keterangan }}</dd>
                <dt class="text-gray-500">Lampiran</dt><dd>{{ $surat->lampiran }}</dd>
                <dt class="text-gray-500">Kepada</dt><dd class="whitespace-pre-line">{{ $surat->tujuan_surat }}</dd>
                <dt class="text-gray-500">Penandatangan</dt><dd>{{ $surat->penandatangan->nama_lengkap }}</dd>
                <dt class="text-gray-500">Dibuat oleh</dt><dd>{{ $surat->pembuat->nama_lengkap }}</dd>
                @if ($d)
                    <dt class="text-gray-500">Kegiatan</dt>
                    <dd>{{ $d->hari_tgl_kegiatan }}<br>{{ $d->waktu_kegiatan }}<br>{{ $d->tempat_kegiatan }}</dd>
                @endif
                @if ($surat->status_surat !== 'Draft')
                    <dt class="text-gray-500">ACC Kepsek</dt>
                    <dd>{{ $surat->tgl_acc?->locale('id')->translatedFormat('d M Y H:i') }} @if ($surat->catatan_acc) · {{ $surat->catatan_acc }} @endif</dd>
                    <dt class="text-gray-500">Verifikasi</dt>
                    <dd><a class="break-all text-orange-700 hover:underline" target="_blank" href="{{ route('verifikasi', $surat->kode_verifikasi) }}">{{ route('verifikasi', $surat->kode_verifikasi) }}</a></dd>
                    <dt class="text-gray-500">SHA-256</dt><dd class="break-all font-mono text-[11px] text-gray-500">{{ $surat->hash_dokumen }}</dd>
                @endif
            </dl>
        </section>

        @if ($surat->status_surat === 'Draft')
            <section class="card space-y-4" x-data="{ acc: false }">
                <h2 class="text-sm font-bold">Finalisasi</h2>
                <form method="POST" action="{{ route('surat.finalisasi', $surat) }}" class="space-y-3">
                    @csrf
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-3">
                        <input type="checkbox" name="acc" value="1" x-model="acc" class="mt-0.5 h-4 w-4 accent-orange-600">
                        <span class="text-sm font-semibold">Surat ini sudah disetujui Kepala Sekolah</span>
                    </label>
                    <input name="catatan_acc" class="inp" placeholder="Catatan persetujuan (opsional)">
                    <button class="btn-primary" :disabled="!acc">Finalisasi &amp; Buat PDF</button>
                </form>
                <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-4">
                    <a href="{{ route('surat.edit', $surat) }}" class="btn-ghost">Edit draft</a>
                    <form method="POST" action="{{ route('surat.destroy', $surat) }}" onsubmit="return confirm('Hapus draft ini?')">
                        @csrf @method('DELETE')
                        <button class="btn-ghost !text-red-600">Hapus draft</button>
                    </form>
                </div>
            </section>
        @elseif ($surat->status_surat === 'Final')
            <section class="card space-y-4">
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('surat.pdf', [$surat, 'unduh' => 1]) }}" class="btn-primary">Unduh PDF</a>
                    <a href="{{ route('surat.pdf', $surat) }}" target="_blank" class="btn-ghost">Buka di tab baru</a>
                </div>
                <form method="POST" action="{{ route('surat.batal', $surat) }}" class="flex flex-wrap gap-2 border-t border-gray-100 pt-4"
                      onsubmit="return confirm('Batalkan surat ini? Nomor tidak akan dipakai ulang.')">
                    @csrf
                    <input name="alasan" required class="inp flex-1" placeholder="Alasan pembatalan">
                    <button class="btn-ghost !text-red-600">Batalkan surat</button>
                </form>
            </section>
        @endif

        <section class="card">
            <h2 class="mb-3 text-sm font-bold">Riwayat</h2>
            <ul class="space-y-2 text-xs text-gray-600">
                @foreach ($logs as $l)
                    <li><span class="font-semibold text-gray-800">{{ $l->aksi }}</span>
                        · {{ $l->user?->nama_lengkap ?? '-' }} · {{ $l->created_at->locale('id')->translatedFormat('d M Y H:i') }}
                        @if ($l->keterangan) <span class="text-gray-400">· {{ $l->keterangan }}</span> @endif
                    </li>
                @endforeach
            </ul>
        </section>
    </div>

    <div class="mt-5 lg:mt-0">
        @if ($surat->path_pdf)
            <div x-data="{ buka: false }" class="card space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-sm font-bold">Dokumen surat</h2>
                    <button type="button" class="btn-ghost !py-2" @click="buka = !buka" x-text="buka ? 'Sembunyikan' : 'Tampilkan surat'"></button>
                </div>
                <p x-show="!buka" class="py-12 text-center text-sm text-gray-400">Surat tidak dibuka otomatis. Klik "Tampilkan surat" kalau mau melihat isinya.</p>
                <template x-if="buka">
                    <iframe src="{{ route('surat.pdf', $surat) }}" class="h-[75vh] w-full rounded-xl border border-gray-200"></iframe>
                </template>
            </div>
        @else
            <div class="card flex h-64 items-center justify-center text-center text-sm text-gray-400">PDF dibuat saat surat difinalisasi.</div>
        @endif
    </div>
</div>
@endsection