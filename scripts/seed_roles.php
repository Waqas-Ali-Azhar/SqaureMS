<?php

require_once __DIR__ . '/bootstrap.php';

$roles = [
    'admin',
    'manager',
    'project_coordinator',
    'developer',
    'tester',
    'content_writer',
    'designer',
    'business manager',
];

seedMissingLookupValues($conn, 'roles', $roles);
