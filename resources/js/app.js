import Alpine from 'alpinejs';
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import TextAlign from '@tiptap/extension-text-align';
import Table from '@tiptap/extension-table';
import TableRow from '@tiptap/extension-table-row';
import TableHeader from '@tiptap/extension-table-header';
import TableCell from '@tiptap/extension-table-cell';

window.Alpine = Alpine;

Alpine.data('richEditor', (initial = '', name = '') => {
    let editor = null; // disimpan di luar state Alpine supaya tidak dibungkus proxy

    return {
        html: initial,
        tick: 0,

        init() {
            editor = new Editor({
                element: this.$refs.el,
                extensions: [
                    StarterKit,
                    Underline,
                    TextAlign.configure({ types: ['paragraph', 'heading'] }),
                    Table.configure({ resizable: false }),
                    TableRow, TableHeader, TableCell,
                ],
                content: initial,
                editorProps: {
                    attributes: { class: 'isi min-h-28 px-3.5 py-2.5 text-sm text-gray-800 outline-none' },
                },
                onUpdate: ({ editor }) => {
                    this.html = editor.isEmpty ? '' : editor.getHTML();
                    this.$dispatch('rich-change', { name, html: this.html });
                },
                onTransaction: () => { this.tick++; },
            });
        },

        destroy() { if (editor) editor.destroy(); },

        on(a, b) { this.tick; return editor ? editor.isActive(a, b) : false; },
        get inTable() { this.tick; return editor ? editor.isActive('table') : false; },
        run(fn) { if (editor) fn(editor.chain().focus()).run(); },
    };
});

Alpine.start();