<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cek Kode Surat · SMK Kosgoro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center p-4 font-sans antialiased" style="background:linear-gradient(160deg,#fff7ed 0,#f9fafb 55%,#f3f4f6 100%)">
    <form method="POST" action="{{ route('verifikasi.cari') }}" class="card w-full max-w-sm space-y-4 !p-6">
        @csrf
        <div class="text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-600 text-base font-extrabold text-white">SK</div>
            <h1 class="text-lg font-extrabold">Cek Keaslian Surat</h1>
            <p class="text-sm text-gray-500">Masukkan kode yang tertera di surat.</p>
        </div>
        <input name="kode" class="inp text-center font-mono text-lg font-bold uppercase tracking-widest" maxlength="12" placeholder="K7M4QX" autofocus>
        @error('kode') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
        <button class="btn-primary w-full">Cek</button>
    </form>
</body>
</html>