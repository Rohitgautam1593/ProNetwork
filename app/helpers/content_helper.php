<?php
/**
 * Normalize user-authored post/comment text (strip accidental leading indent, NBSP, etc.).
 */
function pn_normalize_post_content(?string $content): string
{
    if ($content === null || $content === '') {
        return '';
    }

    $content = preg_replace('/^\x{FEFF}/u', '', $content);
    $lines = preg_split('/\R/u', $content) ?: [];
    $strip = '/^[\s\x{00A0}\x{1680}\x{2000}-\x{200A}\x{202F}\x{205F}\x{3000}\x{FEFF}]+|[\s\x{00A0}\x{1680}\x{2000}-\x{200A}\x{202F}\x{205F}\x{3000}\x{FEFF}]+$/u';

    $lines = array_map(static function (string $line) use ($strip): string {
        return preg_replace($strip, '', $line);
    }, $lines);

    return trim(implode("\n", $lines));
}
