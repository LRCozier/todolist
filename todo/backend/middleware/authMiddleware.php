<?php

require_once __DIR__ . '/../config/auth.php';

function authenticateRequest() {
  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    return true;
  }

  $token = getBearerToken();

  if (!$token) {
    http_response_code(401);
    echo json_encode(['success' => false,
    'error' => 'Authorisation token required']);
    exit;
  }

  $userData = validateJWT($token);

  if (!$userData) {
    http_response_code(401);
    echo json_encode(['success' => false,
    'error' => 'Invalid or expired token']);
    exit;
  }

  return $userData;
  
}
?>