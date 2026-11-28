<?php

require_once __DIR__ . '/bootstrap.php';

$plugins = [
    [
        'plugin_name' => 'WP Super Cache',
        'plugin_type' => 'cache,cdn',
    ],
    [
        'plugin_name' => 'Autoptimize',
        'plugin_type' => 'cache,optimization,image optimization,cdn',
    ],
];

seedMissingPlugins($conn, $plugins);
