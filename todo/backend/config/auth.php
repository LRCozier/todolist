<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
  $secret = $_ENV['JWT_SECRET'] ?? 'secret-key';

  try{
    $decoded = JWT::decode($token, new Key($secret, 'HS256'));
    return (array) $decoded; 
  } catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
      'success' => false,
      'error' => 'Invalid token: ' . $e->getMessage()
    ]);
    exit;
  }
}

function verifyTaskOwnership(PDO $pdo, $taskId, $userId) {
  $stmt = $pdo->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
  $stmt->execute([$taskId, $userId]);

  if(!$stmt->fetch()) {
    http_response_code(403);
    echo json_encode([
      'success' => false,
      'error' => 'Task not found - Access denied'
    ]);

    exit;
  }
}

?>