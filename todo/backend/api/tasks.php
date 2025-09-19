<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/authMiddleware.php';
require_once __DIR__ . '/../utils/validation.php';

header('Content-Type: application/json');

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

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Authenticate the request
$userData = authenticateRequest();
$userId = $userData['userId'];

$pdo = getPDO();

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $taskId = $_GET['id'] ?? null;
    $data = null;
    
    if ($method !== 'GET' && $method !== 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        $data = sanitizeInput($data);
    }

    switch ($method) {
        case 'GET':
            if ($taskId) {
                // Get single task
                $stmt = $pdo->prepare("SELECT id, title, description, completed, created_at, updated_at FROM tasks WHERE id = ? AND user_id = ?");
                $stmt->execute([$taskId, $userId]);
                $task = $stmt->fetch();
                
                if (!$task) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'error' => 'Task not found']);
                    exit;
                }
                
                echo json_encode(['success' => true, 'data' => $task]);
            } else {
                // Get all tasks
                $stmt = $pdo->prepare("SELECT id, title, description, completed, created_at, updated_at FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
                $stmt->execute([$userId]);
                $tasks = $stmt->fetchAll();
                
                echo json_encode(['success' => true, 'data' => $tasks]);
            }
            break;

        case 'POST':
            $validation = validateTaskData($data);
            if ($validation !== true) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => implode(', ', $validation)]);
                exit;
            }
            
            $stmt = $pdo->prepare("INSERT INTO tasks (title, description, user_id) VALUES (?, ?, ?)");
            $stmt->execute([
                $data['title'],
                $data['description'] ?? null,
                $userId
            ]);
            
            $newTaskId = $pdo->lastInsertId();
            
            // Fetch the complete task to return
            $stmt = $pdo->prepare("SELECT id, title, description, completed, created_at, updated_at FROM tasks WHERE id = ?");
            $stmt->execute([$newTaskId]);
            $newTask = $stmt->fetch();
            
            http_response_code(201);
            echo json_encode(['success' => true, 'data' => $newTask]);
            break;

        case 'PUT':
            if (!$taskId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Task ID is required']);
                exit;
            }
            
            // Check if task exists and belongs to user
            $stmt = $pdo->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
            $stmt->execute([$taskId, $userId]);
            
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Task not found']);
                exit;
            }
            
            $validation = validateTaskData($data);
            if ($validation !== true) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => implode(', ', $validation)]);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, completed = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([
                $data['title'],
                $data['description'] ?? null,
                $data['completed'] ?? false,
                $taskId,
                $userId
            ]);

            // Fetch the updated task
            $stmt = $pdo->prepare("SELECT id, title, description, completed, created_at, updated_at FROM tasks WHERE id = ?");
            $stmt->execute([$taskId]);
            $updatedTask = $stmt->fetch();
            
            echo json_encode(['success' => true, 'data' => $updatedTask]);
            break;

        case 'DELETE':
            if (!$taskId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Task ID is required']);
                exit;
            }

            // Check if task exists and belongs to user
            $stmt = $pdo->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
            $stmt->execute([$taskId, $userId]);
            
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Task not found']);
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
            $stmt->execute([$taskId, $userId]);
            
            echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error']);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
?>
