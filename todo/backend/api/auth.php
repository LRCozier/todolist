<?php
require_once __DIR__ . '/config/database.php';

$pdo = getPDO();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? null;

    if (!$action) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Action (login or register) is required.']);
        exit;
    }

    switch ($action) {
        case 'register':
            $username = $data['username'] ?? '';
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';

            if (empty($username) || empty($email) || empty($password)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Please fill all fields.']);
                exit;
            }

            // Hash the password securely
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into the database
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $passwordHash])) {
                http_response_code(201); // Created
                echo json_encode(['success' => true, 'message' => 'Registration successful!']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to register user.']);
            }
            break;

        case 'login':
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';

            if (empty($username) || empty($password)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Please fill all fields.']);
                exit;
            }

            // Find user by username or email
            $stmt = $pdo->prepare("SELECT id, username, email, password_hash FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            // Verify password
            if ($user && password_verify($password, $user['password_hash'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['loggedin'] = true;

                echo json_encode(['success' => true, 'message' => 'Login successful!', 'user' => ['username' => $user['username']]]);
            } else {
                http_response_code(401); // Unauthorized
                echo json_encode(['success' => false, 'error' => 'Invalid username or password.']);
            }
            break;

        case 'logout':
            // Unset all session variables and destroy the session
            $_SESSION = array();
            session_destroy();
            echo json_encode(['success' => true, 'message' => 'Logout successful!']);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'An unknown error occurred.']);
}
?>
