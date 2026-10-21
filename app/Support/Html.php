<?php

namespace App\Support;

class Html
{
    private const TAGS = ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'ul', 'ol', 'li', 'table', 'thead',
        'tbody', 'tfoot', 'tr', 'th', 'td', 'h2', 'h3', 'h4', 'blockquote', 'hr', 'span', 'div'];
    private const HAPUS = ['script', 'style', 'iframe', 'object', 'embed', 'link', 'meta', 'form', 'input', 'button', 'svg', 'math'];

    /** Bersihkan HTML. Teks polos lama (tanpa tag) diubah jadi HTML dengan <br>. */
    public static function bersih(?string $html): string
    {
        $html = trim((string) $html);
        if ($html === '') {
            return '';
        }
        if ($html === strip_tags($html)) {
            return nl2br(e($html));
        }

        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8"?><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $root = $doc->getElementsByTagName('div')->item(0);
        self::telusuri($root);

        $out = '';
        foreach ($root->childNodes as $n) {
            $out .= $doc->saveHTML($n);
        }
        return trim($out);
    }

    private static function telusuri(\DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $c) {
            if ($c instanceof \DOMComment) {
                $node->removeChild($c);
                continue;
            }
            if (!$c instanceof \DOMElement) {
                continue;
            }

            $tag = strtolower($c->nodeName);
            if (in_array($tag, self::HAPUS, true)) {
                $node->removeChild($c);
                continue;
            }

            self::telusuri($c);

            if (!in_array($tag, self::TAGS, true)) {
                while ($c->firstChild) {
                    $node->insertBefore($c->firstChild, $c);
                }
                $node->removeChild($c);
                continue;
            }

            foreach (iterator_to_array($c->attributes) as $a) {
                $nm = strtolower($a->nodeName);
                if (in_array($nm, ['colspan', 'rowspan'], true) && ctype_digit($a->nodeValue)) {
                    continue;
                }
                if ($nm === 'style' && preg_match('/text-align:\s*(left|center|right|justify)/i', $a->nodeValue, $m)) {
                    $c->setAttribute('style', 'text-align: ' . strtolower($m[1]));
                    continue;
                }
                $c->removeAttribute($a->nodeName);
            }
        }
    }
}