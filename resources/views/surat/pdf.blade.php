<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
    @page { margin: 28px 54px 34px 54px; }
    body { font-family: "Times New Roman", Times, serif; font-size: 12pt; line-height: 1.4; color: #000; }
    table { border-collapse: collapse; }
    td { padding: 0; vertical-align: top; }

.isi p { margin: 0 0 6px; }
.isi ul, .isi ol { margin: 0 0 6px 18px; padding: 0; }
.isi table { border-collapse: collapse; width: 100%; margin: 6px 0; }
.isi th, .isi td { border: 1px solid #000; padding: 3px 6px; vertical-align: top; text-align: left; }
.isi th { background: #eeeeee; font-weight: bold; }
</style>
</head>
<body>

<table width="100%">
    <tr>
        <td width="88" style="vertical-align:middle">
            @if ($logo) <img src="{{ $logo }}" style="width:78px;height:78px"> @endif
        </td>
        <td style="vertical-align:middle;line-height:1.15">
            <div style="font-size:15pt;font-weight:bold">{{ $kop['nama_yayasan'] ?? '' }}</div>
            <div style="font-size:9pt;font-weight:bold;color:#c2410c">{{ $kop['nama_program'] ?? '' }}</div>
            <div style="font-size:21pt;font-weight:bold;color:#c2410c">{{ $kop['nama_sekolah'] ?? '' }}</div>
            <div style="font-size:8pt;font-weight:bold;margin-top:2px">{{ $kop['alamat'] ?? '' }}</div>
            <div style="font-size:8pt;font-weight:bold">TELEPON: {{ $kop['telepon'] ?? '' }}, WEBSITE: {{ $kop['website'] ?? '' }}, E-MAIL: {{ $kop['email'] ?? '' }}</div>
        </td>
        <td width="150" style="vertical-align:middle;font-size:9pt;font-weight:bold;line-height:1.3">
            <div>{{ $kop['akreditasi'] ?? '' }}</div>
            <div>NSS: {{ $kop['nss'] ?? '' }}</div>
            <div>NPSN: {{ $kop['npsn'] ?? '' }}</div>
        </td>
    </tr>
</table>
<div style="border-top:3px double #000;margin:6px 0 16px"></div>

<table width="100%">
    <tr>
        <td>
            <table>
                <tr><td width="80">Nomor</td><td>: {{ $s->nomor_lengkap }}</td></tr>
                <tr><td>Lampiran</td><td>: {{ $s->lampiran }}</td></tr>
                <tr><td>Perihal</td><td style="font-weight:bold">: {{ $s->perihal }}</td></tr>
            </table>
        </td>
        <td width="150" align="right">{{ $s->tgl_surat->locale('id')->translatedFormat('d F Y') }}</td>
    </tr>
</table>

<div style="margin-top:22px">
    <div>Yth.,</div>
    <div style="font-weight:bold">{!! nl2br(e($s->tujuan_surat)) !!}</div>
    <div>di Tempat</div>
</div>

<div class="isi" style="margin-top:20px;text-align:justify">{!! \App\Support\Html::bersih($s->paragraf_pembuka) !!}</div>

@if ($s->detail)
    <table style="margin:12px 0 0 30px">
        <tr><td width="110">Hari/Tanggal</td><td>: {{ $s->detail->hari_tgl_kegiatan }}</td></tr>
        <tr><td style="padding-top:4px">Pukul</td><td style="padding-top:4px">: {{ $s->detail->waktu_kegiatan }}</td></tr>
        <tr><td style="padding-top:4px">Tempat</td><td style="padding-top:4px">: {!! nl2br(e($s->detail->tempat_kegiatan)) !!}</td></tr>
    </table>
@endif

<div class="isi" style="margin-top:16px;text-align:justify">{!! \App\Support\Html::bersih($s->paragraf_penutup) !!}</div>

<table width="100%" style="margin-top:24px;page-break-inside:avoid">
    <tr>
        <td valign="bottom">
            @if ($qr)
                <img src="{{ $qr }}" style="width:74px;height:74px">
                <div style="font-size:7pt;color:#555;width:190px;line-height:1.25">
    Dokumen disahkan secara elektronik.<br>
    Kode: <b>{{ $s->kode_verifikasi }}</b><br>
    {{ route('verifikasi', $s->kode_verifikasi) }}
</div>
            @endif
        </td>
        <td width="260" style="text-align:left">
            <div style="font-weight:bold">{{ $s->penandatangan->jabatan }},</div>
            @if ($ttd)
                <img src="{{ $ttd }}" style="height:92px">
            @else
                <div style="height:92px"></div>
            @endif
            <div style="font-weight:bold;text-decoration:underline">{{ $s->penandatangan->nama_lengkap }}</div>
            @if ($s->penandatangan->nip)
                <div style="font-size:10pt">NIP. {{ $s->penandatangan->nip }}</div>
            @endif
        </td>
    </tr>
</table>

</body>
</html>