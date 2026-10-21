<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="lbl">Nama lengkap *</label>
        <input name="nama_lengkap" class="inp" value="{{ old('nama_lengkap', $p->nama_lengkap ?? '') }}" required>
    </div>
    <div>
        <label class="lbl">Jabatan *</label>
        <input name="jabatan" class="inp" value="{{ old('jabatan', $p->jabatan ?? 'Kepala Sekolah') }}" required>
    </div>
    <div>
        <label class="lbl">NIP</label>
        <input name="nip" class="inp" value="{{ old('nip', $p->nip ?? '') }}">
    </div>
    <div>
        <label class="lbl">Gambar tanda tangan &amp; stempel</label>
        <input type="file" name="ttd" accept="image/*" class="inp !py-2 text-xs file:mr-3 file:rounded-lg file:border-0 file:bg-orange-50 file:px-3 file:py-1.5 file:font-semibold file:text-orange-700">
        <p class="mt-1 text-[11px] text-gray-400">Disarankan PNG berlatar transparan (ttd + stempel jadi satu), maks 2 MB.</p>
    </div>
</div>
@if (!empty($p?->file_ttd_stempel))
    <img src="{{ asset('storage/' . $p->file_ttd_stempel) }}" class="mt-3 h-20 rounded-lg border border-gray-200 bg-gray-50 p-1">
@endif
<label class="mt-4 flex items-center gap-2 text-sm text-gray-700">
    <input type="checkbox" name="aktif" value="1" class="h-4 w-4 accent-orange-600" @checked(old('aktif', $p->aktif ?? true))> Aktif (muncul di pilihan penandatangan)
</label>