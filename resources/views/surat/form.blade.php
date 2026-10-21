@extends('layouts.app')
@section('title', $surat->exists ? 'Edit Draft Surat' : 'Buat Surat Keluar')

@section('content')
@php
    $d   = $surat->detail;
    $old = session()->hasOldInput();
    $cfg = [
        'nomorMap'    => $nomorMap,
        'klasifikasi' => $klasifikasi->map(fn ($k) => ['id' => $k->id_klasifikasi, 'kode' => $k->kode_indeks])->values(),
        'pegawai'     => $pegawai->map(fn ($p) => [
            'id' => $p->id_pegawai, 'nama' => $p->nama_lengkap, 'jabatan' => $p->jabatan,
            'ttd' => $p->file_ttd_stempel ? asset('storage/' . $p->file_ttd_stempel) : null,
        ])->values(),
        'kop' => $kop,
        'f' => [
            'id_klasifikasi'    => (int) old('id_klasifikasi', $surat->id_klasifikasi ?? $klasifikasi->first()?->id_klasifikasi),
            'id_penandatangan'  => (int) old('id_penandatangan', $surat->id_penandatangan ?? $pegawai->first()?->id_pegawai),
            'tgl_surat'         => old('tgl_surat', $surat->tgl_surat?->format('Y-m-d') ?? now()->format('Y-m-d')),
            'perihal'           => old('perihal', $surat->perihal ?? ''),
            'lampiran'          => old('lampiran', $surat->lampiran ?? '-'),
            'tujuan_surat'      => old('tujuan_surat', $surat->tujuan_surat ?? ''),
            'paragraf_pembuka'  => \App\Support\Html::bersih(old('paragraf_pembuka', $surat->paragraf_pembuka ?? 'Dengan hormat,')),
            'ada_kegiatan'      => $old ? old('ada_kegiatan') !== null : $d !== null,
            'hari_tgl_kegiatan' => old('hari_tgl_kegiatan', $d->hari_tgl_kegiatan ?? ''),
            'waktu_kegiatan'    => old('waktu_kegiatan', $d->waktu_kegiatan ?? ''),
            'tempat_kegiatan'   => old('tempat_kegiatan', $d->tempat_kegiatan ?? ''),
            'paragraf_penutup'  => \App\Support\Html::bersih(old('paragraf_penutup', $surat->paragraf_penutup ?? 'Demikian surat ini kami sampaikan. Atas perhatiannya kami ucapkan terima kasih.')),
            'acc'               => $old ? old('acc') !== null : false,
            'catatan_acc'       => old('catatan_acc', ''),
        ],
    ];
@endphp

<form method="POST" action="{{ $surat->exists ? route('surat.update', $surat) : route('surat.store') }}"
      x-data="suratForm(@js($cfg))"
      @rich-change="f[$event.detail.name] = $event.detail.html">
    @csrf
    @if ($surat->exists) @method('PUT') @endif

    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight">{{ $surat->exists ? 'Edit Draft Surat' : 'Buat Surat Keluar' }}</h1>
            <p class="mt-1 text-sm text-gray-500">Isi form di kiri, lembar surat langsung ter-update di kanan.</p>
        </div>
        @include('partials.badge', ['status' => 'Draft'])
    </div>

    <div class="mb-4 grid grid-cols-2 rounded-xl bg-gray-100 p-1 text-sm font-semibold lg:hidden">
        <button type="button" @click="tab = 'form'" class="rounded-lg py-2" :class="tab === 'form' ? 'bg-white text-orange-700 shadow-sm' : 'text-gray-500'">Form</button>
        <button type="button" @click="tab = 'preview'" class="rounded-lg py-2" :class="tab === 'preview' ? 'bg-white text-orange-700 shadow-sm' : 'text-gray-500'">Pratinjau</button>
    </div>

    <div class="gap-6 lg:grid lg:grid-cols-[minmax(0,1fr)_minmax(0,1.05fr)]">

        {{-- ============ FORM ============ --}}
        <div class="space-y-5" :class="{ 'hidden lg:block': tab === 'preview' }">

            <section class="card">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-bold">Formula Penomoran Otomatis</h2>
                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-semibold text-gray-500">Terkunci sistem</span>
                </div>
                <div class="flex flex-wrap items-center gap-1.5 rounded-xl bg-gray-50 p-3 text-sm">
                    <span class="rounded-lg border border-orange-300 bg-orange-50 px-2.5 py-1 font-bold text-orange-700" x-text="nomorUrut"></span>
                    <span class="text-gray-400">/</span>
                    <span class="rounded-lg border border-gray-200 bg-white px-2.5 py-1 font-semibold text-gray-600">SMK-Kosgoro</span>
                    <span class="text-gray-400">/</span>
                    <span class="rounded-lg border border-orange-300 bg-orange-50 px-2.5 py-1 font-bold text-orange-700" x-text="klas.kode"></span>
                    <span class="text-gray-400">/</span>
                    <span class="rounded-lg border border-gray-200 bg-white px-2.5 py-1 font-semibold text-gray-600" x-text="romawi"></span>
                    <span class="text-gray-400">/</span>
                    <span class="rounded-lg border border-gray-200 bg-white px-2.5 py-1 font-semibold text-gray-600" x-text="tahun"></span>
                </div>
                <p class="mt-3 text-xs text-gray-500">
                    Nomor: <span class="font-mono font-semibold text-gray-800" x-text="nomor"></span>.
                    Nomor urut baru dikunci saat Finalisasi, jadi draft tidak memakan nomor.
                </p>
            </section>

            <section class="card space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold">Metadata Header Surat</h2>
                    <span class="text-xs text-gray-400">Langkah 1 dari 3</span>
                </div>
                <div>
                    <label class="lbl">Klasifikasi surat *</label>
                    <select name="id_klasifikasi" class="inp" x-model="f.id_klasifikasi">
                        @foreach ($klasifikasi as $k)
                            <option value="{{ $k->id_klasifikasi }}">{{ $k->kode_indeks }} - {{ $k->keterangan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="lbl">Tanggal surat *</label>
                        <input type="date" name="tgl_surat" class="inp" x-model="f.tgl_surat">
                        <p class="mt-1 text-[11px] text-gray-400">Otomatis romawi: <span x-text="romawi"></span> | Tahun: <span x-text="tahun"></span></p>
                    </div>
                    <div>
                        <label class="lbl">Nomor urut surat</label>
                        <input type="text" class="inp bg-gray-50 font-mono font-semibold text-orange-700" :value="nomorUrut" readonly>
                    </div>
                </div>
                <div>
                    <label class="lbl">Perihal *</label>
                    <input type="text" name="perihal" class="inp" x-model="f.perihal">
                </div>
                <div>
                    <label class="lbl">Lampiran</label>
                    <input type="text" name="lampiran" class="inp sm:max-w-xs" x-model="f.lampiran" placeholder="-">
                </div>
                <div>
                    <label class="lbl">Tujuan surat (Yth.) *</label>
                    <textarea name="tujuan_surat" rows="3" class="inp" x-model="f.tujuan_surat" placeholder="Bapak/Ibu Orangtua / Wali&#10;Peserta Didik Kelas 11"></textarea>
                </div>
            </section>

            <section class="card space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold">Isi &amp; Uraian Surat</h2>
                    <span class="text-xs text-gray-400">Langkah 2 dari 3</span>
                </div>

                <div>
                    <label class="lbl">Paragraf pembuka</label>
                    @include('partials.rich-editor', ['name' => 'paragraf_pembuka', 'value' => $cfg['f']['paragraf_pembuka']])
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <label class="flex cursor-pointer items-center justify-between">
                        <span class="text-sm font-semibold">Detail kegiatan (khusus undangan)</span>
                        <input type="checkbox" name="ada_kegiatan" x-model="f.ada_kegiatan" class="peer sr-only">
                        <span class="relative h-6 w-11 rounded-full bg-gray-300 transition peer-checked:bg-orange-500 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></span>
                    </label>
                    <div x-show="f.ada_kegiatan" x-transition class="mt-4 space-y-3">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="lbl">Hari &amp; tanggal</label>
                                <input type="text" name="hari_tgl_kegiatan" class="inp" x-model="f.hari_tgl_kegiatan" placeholder="Jum'at, 04 September 2026">
                            </div>
                            <div>
                                <label class="lbl">Waktu</label>
                                <input type="text" name="waktu_kegiatan" class="inp" x-model="f.waktu_kegiatan" placeholder="13:00 s.d selesai.">
                            </div>
                        </div>
                        <div>
                            <label class="lbl">Tempat kegiatan</label>
                            <textarea name="tempat_kegiatan" rows="2" class="inp" x-model="f.tempat_kegiatan"></textarea>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="lbl">Paragraf penutup</label>
                    @include('partials.rich-editor', ['name' => 'paragraf_penutup', 'value' => $cfg['f']['paragraf_penutup']])
                </div>
            </section>

            <section class="card space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold">Penandatangan &amp; Finalisasi</h2>
                    <span class="text-xs text-gray-400">Langkah 3 dari 3</span>
                </div>
                <div>
                    <label class="lbl">Penandatangan *</label>
                    <select name="id_penandatangan" class="inp" x-model="f.id_penandatangan">
                        @foreach ($pegawai as $p)
                            <option value="{{ $p->id_pegawai }}">{{ $p->nama_lengkap }} - {{ $p->jabatan }}</option>
                        @endforeach
                    </select>
                    @if ($pegawai->isEmpty())
                        <p class="mt-1 text-xs text-red-600">Belum ada penandatangan aktif. Tambahkan dulu di menu Penandatangan.</p>
                    @endif
                </div>

                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-3 transition hover:border-orange-300"
                       :class="f.acc && 'border-orange-400 bg-orange-50'">
                    <input type="checkbox" name="acc" x-model="f.acc" class="mt-0.5 h-4 w-4 rounded border-gray-300 accent-orange-600">
                    <span class="text-sm">
                        <span class="font-semibold">Surat ini sudah disetujui Kepala Sekolah</span>
                        <span class="block text-xs text-gray-500">Wajib dicentang sebelum Finalisasi. Tanda tangan &amp; stempel baru ditempel setelah ini.</span>
                    </span>
                </label>
                <div x-show="f.acc" x-transition>
                    <label class="lbl">Catatan persetujuan (opsional)</label>
                    <input type="text" name="catatan_acc" class="inp" x-model="f.catatan_acc" placeholder="mis. ACC lisan / via WhatsApp">
                </div>

                <div class="rounded-xl bg-gray-50 p-3 text-xs text-gray-500">
                    Nama file: <span class="break-all font-mono font-semibold text-gray-700" x-text="namaFile"></span>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <button type="submit" name="aksi" value="final" class="btn-primary" :disabled="!f.acc">Finalisasi &amp; Buat PDF</button>
                    <button type="submit" name="aksi" value="draft" class="btn-ghost">Simpan Draft</button>
                </div>
            </section>
        </div>

        {{-- ============ PRATINJAU ============ --}}
        <div class="lg:sticky lg:top-24 lg:self-start" :class="{ 'hidden lg:block': tab === 'form' }">
            <section class="card !p-4">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-bold">Pratinjau Lembar Surat (A4)</h2>
                    <span class="text-[11px] text-gray-400">Skala <span x-text="Math.round(scale * 100)"></span>%</span>
                </div>

                <div x-ref="wrap" class="relative w-full overflow-hidden rounded-xl bg-gray-100 ring-1 ring-gray-200" :style="`height:${1123 * scale}px`">
                    <div class="origin-top-left overflow-hidden bg-white text-black"
                         style="width:794px;height:1123px;padding:44px 64px;font-family:'Times New Roman',Times,serif;font-size:15px;line-height:1.45"
                         :style="`transform:scale(${scale})`">

                        <div style="display:flex;align-items:center;gap:16px;border-bottom:3px double #111;padding-bottom:10px">
                            <template x-if="kop.logo_url"><img :src="kop.logo_url" style="width:80px;height:80px;object-fit:contain"></template>
                            <template x-if="!kop.logo_url"><div style="width:80px;height:80px;border-radius:50%;border:4px solid #ea580c;display:flex;align-items:center;justify-content:center;font:800 24px sans-serif;color:#ea580c">SMK</div></template>
                            <div style="flex:1;line-height:1.15">
                                <div style="font-weight:700;font-size:19px" x-text="kop.nama_yayasan"></div>
                                <div style="font-weight:700;font-size:12px;color:#c2410c" x-text="kop.nama_program"></div>
                                <div style="font-weight:800;font-size:28px;color:#c2410c" x-text="kop.nama_sekolah"></div>
                                <div style="font-weight:700;font-size:10.5px;margin-top:2px" x-text="kop.alamat"></div>
                                <div style="font-weight:700;font-size:10.5px">TELEPON: <span x-text="kop.telepon"></span>, WEBSITE: <span x-text="kop.website"></span>, E-MAIL: <span x-text="kop.email"></span></div>
                            </div>
                            <div style="font-weight:700;font-size:12px;line-height:1.35;max-width:140px">
                                <div x-text="kop.akreditasi"></div>
                                <div>NSS: <span x-text="kop.nss"></span></div>
                                <div>NPSN: <span x-text="kop.npsn"></span></div>
                            </div>
                        </div>

                        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-top:18px">
                            <table style="max-width:480px">
                                <tr><td style="width:80px;vertical-align:top">Nomor</td><td>: <span x-text="nomor"></span></td></tr>
                                <tr><td style="vertical-align:top">Lampiran</td><td>: <span x-text="f.lampiran || '-'"></span></td></tr>
                                <tr><td style="vertical-align:top">Perihal</td><td style="font-weight:700">: <span x-text="f.perihal"></span></td></tr>
                            </table>
                            <div x-text="tglIndo"></div>
                        </div>

                        <div style="margin-top:22px">
                            <div>Yth.,</div>
                            <div style="font-weight:700;white-space:pre-line" x-text="f.tujuan_surat"></div>
                            <div>di Tempat</div>
                        </div>

                        <div class="isi" style="margin-top:20px;text-align:justify" x-html="f.paragraf_pembuka"></div>

                        <table x-show="f.ada_kegiatan" style="margin:14px 0 0 28px">
                            <tr><td style="width:110px;vertical-align:top">Hari/Tanggal</td><td>: <span x-text="f.hari_tgl_kegiatan"></span></td></tr>
                            <tr><td style="vertical-align:top;padding-top:4px">Pukul</td><td style="padding-top:4px">: <span x-text="f.waktu_kegiatan"></span></td></tr>
                            <tr><td style="vertical-align:top;padding-top:4px">Tempat</td><td style="padding-top:4px;white-space:pre-line">: <span x-text="f.tempat_kegiatan"></span></td></tr>
                        </table>

                        <div class="isi" style="margin-top:18px;text-align:justify" x-html="f.paragraf_penutup"></div>

                        <div style="margin:28px 0 0 auto;width:270px">
                            <div style="font-weight:700"><span x-text="pen.jabatan"></span>,</div>
                            <div style="height:92px;display:flex;align-items:center;justify-content:center">
                                <template x-if="f.acc && pen.ttd"><img :src="pen.ttd" style="max-height:90px;max-width:100%"></template>
                                <template x-if="f.acc && !pen.ttd"><div style="width:84px;height:84px;border:2px dashed #fdba74;border-radius:50%;display:flex;align-items:center;justify-content:center;text-align:center;font:600 10px/1.2 sans-serif;color:#fb923c">TTD &amp;<br>STEMPEL</div></template>
                            </div>
                            <div style="font-weight:700;text-decoration:underline" x-text="pen.nama"></div>
                        </div>
                    </div>
                </div>
                <p class="mt-3 text-[11px] text-gray-400">Pratinjau ini hampir sama dengan hasil PDF. Posisi akhir ditentukan saat PDF dibuat, dan surat yang terlalu panjang akan lanjut ke halaman berikutnya di PDF.</p>
            </section>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function suratForm(cfg) {
    const ROMAWI = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
    return {
        ...cfg,
        tab: 'form',
        scale: 1,

        init() {
            new ResizeObserver(([e]) => {
                const w = e.contentRect.width;
                if (w > 0) this.scale = Math.min(1, w / 794);
            }).observe(this.$refs.wrap);
        },

        get klas()   { return this.klasifikasi.find(k => k.id == this.f.id_klasifikasi) || { kode: '?' }; },
        get pen()    { return this.pegawai.find(p => p.id == this.f.id_penandatangan) || { nama: '', jabatan: 'Kepala Sekolah', ttd: null }; },
        get tgl()    { return new Date(this.f.tgl_surat + 'T00:00:00'); },
        get valid()  { return !isNaN(this.tgl); },
        get romawi() { return this.valid ? ROMAWI[this.tgl.getMonth()] : '-'; },
        get tahun()  { return this.valid ? this.tgl.getFullYear() : '-'; },
        get nomorUrut() { return this.nomorMap[this.tahun] ?? 1; },
        get nomor()  { return `${this.nomorUrut}/SMK-Kosgoro/${this.klas.kode}/${this.romawi}/${this.tahun}`; },
        get tglIndo() {
            return this.valid
                ? new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }).format(this.tgl)
                : '';
        },
        get namaFile() {
            const slug = this.f.perihal.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')
                .split('-').slice(0, 6).join('-');
            return this.nomor.replace(/\//g, '-') + (slug ? '_' + slug : '') + '.pdf';
        },
    };
}
</script>
@endpush    