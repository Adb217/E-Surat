<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · E-Surat SMK Kosgoro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center p-4 font-sans antialiased"
      style="background:linear-gradient(160deg,#fff7ed 0,#f9fafb 55%,#f3f4f6 100%)">
    <div class="w-full max-w-sm">
        <div class="mb-6 text-center">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-600 text-lg font-extrabold text-white shadow-lg shadow-orange-200">SK</div>
            <h1 class="text-xl font-extrabold text-gray-800">E-Surat SMK Kosgoro</h1>
            <p class="text-sm text-gray-500">Tata Usaha &amp; Persuratan</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="card space-y-4 !p-6">
            @csrf
            @if ($errors->any())
                <div class="rounded-xl bg-red-50 px-3 py-2 text-sm text-red-700">{{ $errors->first() }}</div>
            @endif
            <div>
                <label class="lbl">Username</label>
                <input name="username" value="{{ old('username') }}" class="inp" autofocus autocomplete="username">
            </div>
            <div>
                <label class="lbl">Password</label>
                <input type="password" name="password" class="inp" autocomplete="current-password">
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded accent-orange-600"> Ingat saya
            </label>
            <button class="btn-primary w-full">Masuk</button>
        </form>
    </div>
</body>
</html> 