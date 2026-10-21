<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Surat · SMK Kosgoro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center p-4 font-sans antialiased" style="background:linear-gradient(160deg,#fff7ed 0,#f9fafb 55%,#f3f4f6 100%)">
<div class="w-full max-w-md space-y-4">
    <div class="text-center">
        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-600 text-base font-extrabold text-white">SK</div>
        <h1 class="text-lg font-extrabold">Verifikasi Surat SMK Kosgoro</h1>
    </div>

    @if (!$surat)
        <div class="card text-center">
            <div class="text-sm font-bold text-red-600">Dokumen tidak ditemukan</div>
            <p class="mt-1 text-sm text-gray-500">Kode <span class="font-mono">{{ $kode }}</span> tidak terdaftar. Surat ini mungkin tidak asli.</p>
        </div>
    @else
        <div class="card space-y-3 text-sm">
            @if ($surat->status_surat === 'Final')
                <div class="rounded-xl bg-orange-50 px-3 py-2 font-bold text-orange-700">Dokumen terdaftar dan sah</div>
            @else
                <div class="rounded-xl bg-red-50 px-3 py-2 font-bold text-red-600">Surat ini sudah DIBATALKAN</div>
            @endif
            <dl class="grid grid-cols-[100px_1fr] gap-x-3 gap-y-2">
                <dt class="text-gray-500">Nomor</dt><dd class="font-mono">{{ $surat->nomor_lengkap }}</dd>
                <dt class="text-gray-500">Perihal</dt><dd>{{ $surat->perihal }}</dd>
                <dt class="text-gray-500">Tanggal</dt><dd>{{ $surat->tgl_surat->locale('id')->translatedFormat('d F Y') }}</dd>
                <dt class="text-gray-500">Ditandatangani</dt><dd>{{ $surat->penandatangan->nama_lengkap }}<br><span class="text-xs text-gray-500">{{ $surat->penandatangan->jabatan }}</span></dd>
                <dt class="text-gray-500">SHA-256</dt><dd class="break-all font-mono text-[11px] text-gray-500">{{ $surat->hash_dokumen }}</dd>
            </dl>
        </div>

        <form method="POST" action="{{ route('verifikasi.cek', $kode) }}" enctype="multipart/form-data" class="card space-y-3">
            @csrf
            <div class="text-sm font-bold">Cocokkan file PDF</div>
            <p class="text-xs text-gray-500">Unggah PDF yang Anda terima. Sistem membandingkan sidik jarinya dengan arsip.</p>
            <input type="file" name="pdf" accept="application/pdf" required class="inp !py-2 text-xs">
            @error('pdf') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            <button class="btn-primary w-full">Cek file</button>

            @if ($hasil === 'cocok')
                <div class="rounded-xl bg-orange-50 px-3 py-2 text-sm font-semibold text-orange-700">File identik dengan arsip resmi.</div>
            @elseif ($hasil === 'beda')
                <div class="rounded-xl bg-red-50 px-3 py-2 text-sm font-semibold text-red-600">File TIDAK sama dengan arsip. Dokumen mungkin sudah diubah.</div>
            @endif
        </form>
    @endif
</div>
</body>
</html>