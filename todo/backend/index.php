<?php
require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Set CORS headers
$allowedOrigins = explode(',', $_ENV['ALLOWED_ORIGINS']);
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: " . $allowedOrigins[0]);
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Parse request URL
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestParts = explode('/', trim($requestUri, '/'));

// Route the request
if (count($requestParts) >= 2 && $requestParts[0] === 'api') {
    $resource = $requestParts[1];
    
    if ($resource === 'auth') {
        require __DIR__ . '/api/auth.php';
    } elseif ($resource === 'tasks') {
        require __DIR__ . '/api/tasks.php';
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Endpoint not found']);
    }
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'API not found']);
}
?>
