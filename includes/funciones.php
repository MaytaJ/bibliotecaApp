<?php
function coverClass($cat) {
    $c = strtolower($cat);
    $c = str_replace([' ', 'á','é','í','ó','ú','ñ'], ['-','a','e','i','o','u','n'], $c);
    return 'cover-' . $c;
}

function starsHtml($n) {
    $n = round($n);
    $out = '';
    for ($i = 1; $i <= 5; $i++) {
        $out .= $i <= $n ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star stars-empty"></i>';
    }
    return $out;
}