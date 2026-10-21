@extends('layouts.app')
@section('title', 'Bantuan')

@section('content')
<h1 class="mb-6 text-2xl font-extrabold tracking-tight">Bantuan</h1>
<section class="card max-w-3xl space-y-5 text-sm leading-relaxed text-gray-700">
    <div>
        <h2 class="mb-2 font-bold text-gray-900">Alur membuat surat</h2>
        <ol class="list-decimal space-y-1 pl-5">
            <li>Buka <b>Buat Surat Keluar</b>, pilih klasifikasi, isi perihal, tujuan, dan isi surat.</li>
            <li>Aktifkan <b>Detail kegiatan</b> kalau suratnya undangan (hari, waktu, tempat).</li>
            <li>Simpan sebagai <b>Draft</b> selama masih diperiksa. Draft belum memakan nomor surat.</li>
            <li>Setelah Kepala Sekolah menyetujui, centang konfirmasi lalu klik <b>Finalisasi &amp; Buat PDF</b>.</li>
            <li>Sistem mengunci nomor, menempel tanda tangan &amp; stempel, dan membuat PDF yang bisa diunduh di Arsip.</li>
        </ol>
    </div>
    <div>
        <h2 class="mb-2 font-bold text-gray-900">Surat salah setelah final?</h2>
        <p>Surat final tidak bisa diedit. Batalkan lewat halaman detail (nomornya tidak dipakai ulang), lalu buat surat baru.</p>
    </div>
    <div>
        <h2 class="mb-2 font-bold text-gray-900">Cek keaslian dokumen</h2>
        <p>Scan QR di PDF atau buka tautan verifikasinya. Halaman itu menampilkan data surat dan bisa mencocokkan file PDF yang diunggah dengan arsip.</p>
    </div>
</section>
@endsection