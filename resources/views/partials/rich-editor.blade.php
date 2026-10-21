<div x-data="richEditor(@js($value), @js($name))"
     class="overflow-hidden rounded-xl border border-gray-200 bg-white focus-within:border-orange-500 focus-within:ring-4 focus-within:ring-orange-100">
    <input type="hidden" name="{{ $name }}" :value="html">

    <div class="flex flex-wrap items-center gap-1 overflow-x-auto border-b border-gray-100 bg-gray-50 px-2 py-1.5 text-xs font-semibold">
        <button type="button" title="Tebal" class="rounded-lg px-2.5 py-1 hover:bg-gray-200" :class="on('bold') ? 'bg-orange-100 text-orange-700' : 'text-gray-600'" @click="run(c => c.toggleBold())"><b>B</b></button>
        <button type="button" title="Miring" class="rounded-lg px-2.5 py-1 hover:bg-gray-200" :class="on('italic') ? 'bg-orange-100 text-orange-700' : 'text-gray-600'" @click="run(c => c.toggleItalic())"><i>I</i></button>
        <button type="button" title="Garis bawah" class="rounded-lg px-2.5 py-1 hover:bg-gray-200" :class="on('underline') ? 'bg-orange-100 text-orange-700' : 'text-gray-600'" @click="run(c => c.toggleUnderline())"><u>U</u></button>

        <span class="mx-1 h-4 w-px bg-gray-300"></span>

        <button type="button" class="rounded-lg px-2.5 py-1 hover:bg-gray-200" :class="on('bulletList') ? 'bg-orange-100 text-orange-700' : 'text-gray-600'" @click="run(c => c.toggleBulletList())">• List</button>
        <button type="button" class="rounded-lg px-2.5 py-1 hover:bg-gray-200" :class="on('orderedList') ? 'bg-orange-100 text-orange-700' : 'text-gray-600'" @click="run(c => c.toggleOrderedList())">1. List</button>

        <span class="mx-1 h-4 w-px bg-gray-300"></span>

        <button type="button" title="Rata kiri" class="rounded-lg px-2.5 py-1 hover:bg-gray-200" :class="on({ textAlign: 'left' }) ? 'bg-orange-100 text-orange-700' : 'text-gray-600'" @click="run(c => c.setTextAlign('left'))">Kiri</button>
        <button type="button" title="Rata tengah" class="rounded-lg px-2.5 py-1 hover:bg-gray-200" :class="on({ textAlign: 'center' }) ? 'bg-orange-100 text-orange-700' : 'text-gray-600'" @click="run(c => c.setTextAlign('center'))">Tengah</button>
        <button type="button" title="Rata kanan" class="rounded-lg px-2.5 py-1 hover:bg-gray-200" :class="on({ textAlign: 'right' }) ? 'bg-orange-100 text-orange-700' : 'text-gray-600'" @click="run(c => c.setTextAlign('right'))">Kanan</button>
        <button type="button" title="Rata kiri-kanan" class="rounded-lg px-2.5 py-1 hover:bg-gray-200" :class="on({ textAlign: 'justify' }) ? 'bg-orange-100 text-orange-700' : 'text-gray-600'" @click="run(c => c.setTextAlign('justify'))">Rata</button>

        <span class="mx-1 h-4 w-px bg-gray-300"></span>

        <button type="button" class="rounded-lg bg-orange-50 px-2.5 py-1 text-orange-700 hover:bg-orange-100" @click="run(c => c.insertTable({ rows: 3, cols: 3, withHeaderRow: true }))">+ Tabel</button>
        <button type="button" class="rounded-lg px-2.5 py-1 text-gray-600 hover:bg-gray-200" @click="run(c => c.undo())">Undo</button>
        <button type="button" class="rounded-lg px-2.5 py-1 text-gray-600 hover:bg-gray-200" @click="run(c => c.redo())">Redo</button>
    </div>

    {{-- muncul kalau kursor ada di dalam tabel --}}
    <div x-show="inTable" x-cloak class="flex flex-wrap items-center gap-1 border-b border-orange-100 bg-orange-50 px-2 py-1.5 text-xs font-semibold text-orange-800">
        <span class="mr-1">Tabel:</span>
        <button type="button" class="rounded-lg px-2 py-1 hover:bg-orange-100" @click="run(c => c.addRowAfter())">+ Baris</button>
        <button type="button" class="rounded-lg px-2 py-1 hover:bg-orange-100" @click="run(c => c.addColumnAfter())">+ Kolom</button>
        <button type="button" class="rounded-lg px-2 py-1 hover:bg-orange-100" @click="run(c => c.deleteRow())">- Baris</button>
        <button type="button" class="rounded-lg px-2 py-1 hover:bg-orange-100" @click="run(c => c.deleteColumn())">- Kolom</button>
        <button type="button" class="rounded-lg px-2 py-1 hover:bg-orange-100" @click="run(c => c.toggleHeaderRow())">Baris judul</button>
        <button type="button" class="rounded-lg px-2 py-1 text-red-600 hover:bg-red-50" @click="run(c => c.deleteTable())">Hapus tabel</button>
    </div>

    <div x-ref="el"></div>
</div>