<?php

require_once('config/db.php');

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = '/feed-360/api-v1';
$endpoint = rtrim(str_replace($basePath, '', $requestUri), '/');

// Default route key
$route = '404';

// Match fixed path
if ($endpoint === '/user') {
    $route = 'users_list';
}

elseif ($endpoint === '/project') {
    $route = 'projects_list';
}

elseif ($endpoint === '/master') {
    $route = 'master';
}

elseif ($endpoint === '/general') {
    $route = 'general';
}

elseif ($endpoint === '/specific-data') {
    $route = 'specific_data';
}

elseif ($endpoint === '/master-data') {
    $route = 'master_data';
}

elseif ($endpoint === '/lead') {
    $route = 'lead';
}


// Match /user/{id}
elseif (preg_match('#^/user/(\d+)$#', $endpoint, $matches)) {
    $_GET['id'] = $matches[1]; // Store user ID for the file
    $route = 'user_single';
}

// Match /projects/{slug}
elseif (preg_match('#^/project/([a-zA-Z0-9_-]+)$#', $endpoint, $matches)) {
    $_GET['slug'] = $matches[1]; // Store slug for the file
    $route = 'specific_project';
}

// Match /master/{slug}
elseif (preg_match('#^/master/([a-zA-Z0-9_-]+)$#', $endpoint, $matches)) {
    $_GET['master'] = str_replace("-"," ","$matches[1]"); // Store slug for the file
    $route = 'specific_master';
}

// Match /general/{slug}
elseif (preg_match('#^/general/([a-zA-Z0-9_-]+)$#', $endpoint, $matches)) {
    $_GET['general'] = str_replace("-"," ","$matches[1]"); // Store slug for the file
    $route = 'specific_general';
}

// Match /master/{slug}/general/{slug}/specific/{slug}
if (preg_match('#^/master/([a-zA-Z0-9_-]+)/general/([a-zA-Z0-9_-]+)/specific/([a-zA-Z0-9_-]+)$#', $endpoint, $matches)) {
    $_GET['master']  = str_replace("-", " ", $matches[1]);
    $_GET['general'] = str_replace("-", " ", $matches[2]);
    $_GET['specific'] = str_replace("-", " ", $matches[3]);
    $route = 'project_match';
}




// Use switch on route name
switch ($route) {
    case 'users_list':
        require __DIR__ . '/api/users.php';
        break;

    case 'user_single':
        require __DIR__ . '/api/specific-user.php';
        break;

    case 'projects_list':
        require __DIR__ . '/api/projects.php';
        break;

    case 'specific_project':
        require __DIR__ . '/api/specific-project.php';
        break;

    case 'master':
        require __DIR__ . '/api/master.php';
        break;

    case 'specific_master':
        require __DIR__ . '/api/specific-master.php';
        break;

    case 'general':
        require __DIR__ . '/api/general.php';
        break;

    case 'specific_general':
        require __DIR__ . '/api/specific-general.php';
        break;

    case 'project_match':
        require __DIR__ . '/api/project-match.php';
        break;

    case 'specific_data':
        require __DIR__ . '/api/specific-data.php';
        break;
        
    case 'master_data':
        require __DIR__ . '/api/master-data.php';
        break;
        
    case 'lead':
        require __DIR__ . '/api/lead.php';
        break;

    default:
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}
