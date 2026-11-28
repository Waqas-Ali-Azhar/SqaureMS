<?php

require_once __DIR__ . '/bootstrap.php';

$entities = [
    'lead',
    'contract',
    'assignment',
    'task',
    'client',
    'portfolio',
    'estimation',
    'brand',
    'project',
    'plugin',
];

seedMissingLookupValues($conn, 'entities', $entities);
