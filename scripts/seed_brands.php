<?php

require_once __DIR__ . '/bootstrap.php';

$brands = [
    'TheNextCode',
    'GuddaGudde',
    'SquareBracket',
    'none',
];

seedMissingLookupValues($conn, 'brands', $brands);
