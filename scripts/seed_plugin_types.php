<?php

require_once __DIR__ . '/bootstrap.php';

$pluginTypes = [
    'cache',
    'cdn',
    'optimization',
    'image optimization',
];

seedMissingLookupValues($conn, 'plugin_type', $pluginTypes);
