<?php
function authenticateUser() {
  $headers = getallheaders();
  $authHeader = $headers['Authnorization'] ?? '';

  if(!preg_match(' /Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode([
      'success' => false,
      'error' => 'Authorization token required'
    ]);
    exit;
  }

  $token = $matches[1];
  $secret = 'SECRET_KEY';
}




?>