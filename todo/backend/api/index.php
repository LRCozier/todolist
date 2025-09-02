<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

session_start();

$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

if (isset($requestUri[0]) && $requestUri[0] === 'api') {
    $resource = $requestUri[1] ?? null;

    if ($resource === 'tasks') {
        require __DIR__ . '/tasks.php';
    } elseif ($resource === 'auth') {
        require __DIR__ . '/auth.php';
    } else {
        
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Endpoint not found.']);
    }
} else {
    
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'API not found.']);
}

?>