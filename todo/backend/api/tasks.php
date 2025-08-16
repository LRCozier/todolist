<?php
header("Content-Type: application/json");
require_once __DIR__ . 'config/database.php';

$pdo = getPDO(); // get PDO instance function from database.php

try{
$method = $_SERVER['REQUEST_METHOD'];
$taskId = $_GET['id'] ?? null;

switch($method) {

  case 'GET': //get all tasks
    $stmt = $pdo->query("SELECT * FROM tasks WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $tasks = $stmt ->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode([
      'success' => true,
      'data' => $tasks
    ]);
    break;

  case 'POST': //create a new task
    $data = json_encode(file_get_contents('php://input'), true);

    if(empty($data['title'])){

      http_response_code(400);

      echo json_encode(['success' => false,
      'error' => 'Title cannot be empty']);
      exit;
    }

    $stmt = $pdo->prepare("INSERT INTO tasks (title, description, user_id) VALUES (?, ?, ?)");
    $stmt = $pdo->execute([
      htmlspecialchars($data['title']),
    !empty($data['description']) , null, 
    $user['id']]);
    
    echo json_encode([
      'success' => true,
      'id' => $pdo->lastInsertId()
    ]);
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
} catch(PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false,
      'error' => 'Database error:' . $e->getMessage()]);
    } catch (Exception $e) {
      http_response_code(500);
      echo json_encode(['success' => false,
      'error' => $e->getMessage()]);
    }
?>