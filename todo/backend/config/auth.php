<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateJWT($userId, $username, $email) {
  $secretKey = $_ENV['JWT_SECRET'];
  $issuedAt = time();
  $expire = $issuedAt + $_ENV['JWT_EXPIRE'];

  $payload = [
    'iat' => $issuedAt,
    'exp' => $expire,
    'data' => [
      'userId' => $userId,
      'username' => $username,
      'email' => $email
    ]
  ];

  return JWT::encode($payload, $secretKey, 'HS256');
}

function validateJWT($token) {
  try {
    $secretKey = $_ENV['JWT_SECRET'];
    $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
    return (array) $decoded->data;
  } catch (Exception $e) {
    error_log("JWT Validation Error: " .  $e->getMessage());
    return false;
  }
}

function getBearerToken() {
  $headers = apache_request_headers();

  if(isset($headers['Authorisation'])) {
    $authHeader = $headers['Authorisation'];

    if (preg_match('/Beater\s(\S+)/' , $authHeader, $matches)) {
      return $matches[1];
    }
  }

  return null;
}
?>