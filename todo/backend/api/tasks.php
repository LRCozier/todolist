<?php
header("Content-Type: application/json");
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'You must be logged in to access this resource.']);
    exit;
}

require_once __DIR__ . '/config/database.php';

$pdo = getPDO();
$userId = $_SESSION['user_id'];

try {
  $method = $_SERVER['REQUEST_METHOD'];
  $taskId = $_GET['id'] ?? null;
  $data = null;
  
  if ($method !== 'GET' && $method !== 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
  }

  switch ($method) {
    case 'GET':
      $stmt = $pdo->prepare("SELECT id, title, description, completed, created_at, updated_at FROM tasks WHERE user_id = ?");
      $stmt->execute([$userId]);
      $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode([
        'success' => true,
        'data' => $tasks
      ]);
      break;

    case 'POST':
      if (empty($data['title'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Title cannot be empty']);
        exit;
      }
      $stmt = $pdo->prepare("INSERT INTO tasks (title, description, user_id) VALUES (?, ?, ?)");
      $stmt->execute([
        $data['title'],
        $data['description'] ?? null,
        $userId
      ]);
      $newTask = [
        'id'          => $pdo->lastInsertId(),
        'user_id'     => $userId,
        'title'       => $data['title'],
        'description' => $data['description'] ?? null,
        'completed'   => false,
        'createdAt'   => date('Y-m-d H:i:s'),
        'updatedAt'   => date('Y-m-d H:i:s')
      ];
      echo json_encode(['success' => true, 'data' => $newTask]);
      break;

    case 'PUT':
      if (!$taskId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Task ID is required']);
        exit;
      }
      
      $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, completed = ? WHERE id = ? AND user_id = ?");
      $stmt->execute([
        $data['title'] ?? null,
        $data['description'] ?? null,
        $data['completed'] ?? false,
        $taskId,
        $userId
      ]);

      echo json_encode(['success' => true]);
      break;

    case 'DELETE':
      if (!$taskId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Task ID is required']);
        exit;
      }

      $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
      $stmt->execute([$taskId, $userId]);
      echo json_encode(['success' => true]);
      break;

    default:
      http_response_code(405);
      echo json_encode(['success' => false, 'error' => 'Method not allowed']);
  }
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
