<?php
header("Content-Type: application/json");
require_once 'config.php';

try{
$method = $_SERVER['REQUEST_METHOD'];
switch($method) {

  case 'GET': //get all tasks
    $stmt = $pdo->query("SELECT * FROM tasks");
    echo json_encode($stmt ->fetchAll(PDO::FETCH_ASSOC));
    break;

  case 'POST': //create a new task
    $data = json_encode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO tasks (title, completed) VALUES (?, ?)");
    $stmt = $pdo->execute([$data['title'], false]);
    echo json_encode(['id' => $pdo->lastInsertId()]);
    break;

  case 'PUT'://update task
    $taskId = $_GET['id'] ?? null;
    $data = json_encode(file_get_contents('php://input'), true);
    if (!$taskId) {
      http_response_code(400);
      echo json_encode(['success' => false,
      'error' => 'Task ID is required']);
      exit;
    }

    verifyTaskOwnership($pdo, $taskId, $user['id']); //verify is task belongs to user
    $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, completed = ? WHERE id = ?");
    $stmt->execute([htmlspecialchars($data['title']),
    ! empty($data['description']) ? htmlspecialchars($data['description']) : null,
    $data['completed'] ?? false,
    $taskId]);

    echo json_encode(['success' => true]);
    break;

  case 'DELETE': //delete task
    $taskId = $_GET['id'] ?? null;
    if (!$taskId) {
      http_response_code(400);
      echo json_encode(['success' => false,
      'error' => 'Task ID is required']);
      exit;
    }

    verifyTaskOwnership($pdo, $taskId, $user['id']); //verify is task belongs to user
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$taskId]);
    echo json_encode(['success' => true]);
    break;

    default:
    http_response_code(405);
    echo json_encode(['success' => false, 
    'error' => 'Method not allowed']);

  } 
  catch(PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false,
      'error' => 'Database error:' . $e->getMessage()]);
    } catch (Exception $e) {
      http_response_code(500);
      echo json_encode(['success' => false,
      'error' => $e->getMessage()]);
    }

}
?>