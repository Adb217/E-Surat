@extends('layouts.app')
@section('title', 'Edit Penandatangan')

@section('content')
<h1 class="mb-6 text-2xl font-extrabold tracking-tight">Edit Penandatangan</h1>
<form method="POST" action="{{ route('pegawai.update', $p) }}" enctype="multipart/form-data" class="card max-w-3xl">
    @csrf @method('PUT')
    @include('pegawai._form', ['p' => $p])
    <div class="mt-5 flex gap-3">
        <button class="btn-primary">Simpan</button>
        <a href="{{ route('pegawai.index') }}" class="btn-ghost">Batal</a>
    </div>
</form>
@endsection