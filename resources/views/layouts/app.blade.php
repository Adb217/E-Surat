@php
    $user = auth()->user();
    $icons = [
        'home'    => 'M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
        'plus'    => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
        'archive' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z',
        'tag'     => 'M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3zM6 6h.008v.008H6V6z',
        'pen'     => 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10',
        'help'    => 'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z',
        'sliders' => 'M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75',
        'logout'  => 'M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75',
        'menu'    => 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5',
        'search'  => 'M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z',
        'inbox' => 'M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15M9 12l3 3m0 0l3-3m-3 3V2.25',
    ];
    $icon = fn ($n, $c = 'h-5 w-5') => '<svg class="'.$c.' shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="'.$icons[$n].'"/></svg>';

    $menuUtama = [
        ['Dashboard', route('dashboard'), 'home', request()->routeIs('dashboard')],
        ['Buat Surat Keluar', route('surat.create'), 'plus', request()->routeIs('surat.create')],
        ['Arsip Surat', route('surat.index'), 'archive', request()->routeIs('surat.index', 'surat.show', 'surat.edit')],
        ['Kode Klasifikasi', route('klasifikasi.index'), 'tag', request()->routeIs('klasifikasi.*')],
        ['Penandatangan', route('pegawai.index'), 'pen', request()->routeIs('pegawai.*')],
        ['Buat Surat Keluar', route('surat.create'), 'plus', request()->routeIs('surat.create')],
['Catat Surat Masuk', route('masuk.create'), 'inbox', request()->routeIs('masuk.create', 'masuk.edit')],
['Arsip Surat', route('surat.index'), 'archive', request()->routeIs('surat.index', 'surat.show', 'surat.edit', 'masuk.show')],
    ];
    $menuBawah = [
        ['Bantuan', route('bantuan'), 'help', request()->routeIs('bantuan')],
        ['Pengaturan Sistem', route('pengaturan.edit'), 'sliders', request()->routeIs('pengaturan.*')],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Surat') · SMK Kosgoro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans text-gray-800 antialiased"
      style="background-color:#f9fafb;background-image:linear-gradient(180deg,#fff7ed 0,#f9fafb 340px);background-repeat:no-repeat">

<div x-data="{ menu: false }" class="min-h-screen lg:flex">
    <div x-show="menu" x-cloak x-transition.opacity @click="menu = false" class="fixed inset-0 z-30 bg-gray-900/40 lg:hidden"></div>

    <aside :class="menu ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-gray-200 bg-white transition-transform lg:sticky lg:top-0 lg:h-screen lg:shrink-0">
        <div class="flex items-center gap-3 px-5 py-5">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-600 text-sm font-extrabold text-white">SK</div>
            <div class="leading-tight">
                <div class="text-sm font-extrabold">E-Surat</div>
                <div class="text-[11px] text-gray-500">SMK Kosgoro · Tata Usaha</div>
            </div>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3">
            @foreach ($menuUtama as [$label, $url, $ic, $aktif])
                <a href="{{ $url }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ $aktif ? 'bg-orange-50 text-orange-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    {!! $icon($ic) !!} {{ $label }}
                </a>
            @endforeach
        </nav>

        <div class="space-y-1 border-t border-gray-100 px-3 py-4">
            @foreach ($menuBawah as [$label, $url, $ic, $aktif])
                <a href="{{ $url }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ $aktif ? 'bg-orange-50 text-orange-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    {!! $icon($ic) !!} {{ $label }}
                </a>
            @endforeach
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                    {!! $icon('logout') !!} Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="min-w-0 flex-1">
        <header class="sticky top-0 z-20 flex items-center gap-3 border-b border-gray-200 bg-white/80 px-4 py-3 backdrop-blur sm:px-6 lg:px-8">
            <button type="button" @click="menu = true" class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 lg:hidden">{!! $icon('menu') !!}</button>

            <div class="hidden text-sm text-gray-500 sm:block">
                Beranda <span class="mx-1">/</span> <span class="font-semibold text-orange-700">@yield('title')</span>
            </div>

            <form action="{{ route('surat.index') }}" method="GET" class="relative ml-auto hidden w-72 md:block">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">{!! $icon('search', 'h-4 w-4') !!}</span>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari arsip surat / nomor / perihal..." class="inp !rounded-full !py-2 pl-9">
            </form>

            <div class="ml-auto flex items-center gap-3 md:ml-2">
                <div class="hidden text-right leading-tight sm:block">
                    <div class="text-sm font-bold">{{ $user->nama_lengkap }}</div>
                    <div class="text-[11px] text-gray-500">Staff Tata Usaha</div>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-orange-100 text-sm font-bold text-orange-700">
                    {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8">
            @include('partials.alerts')
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html> 