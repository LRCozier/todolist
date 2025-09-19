<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
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
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$data = sanitizeInput($data);

$action = $data['action'] ?? null;

if (!$action) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Action is required']);
    exit;
}

$pdo = getPDO();

try {
    switch ($action) {
        case 'register':
            $username = $data['username'] ?? '';
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';
            $confirmPassword = $data['confirm_password'] ?? '';

            if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'All fields are required']);
                exit;
            }

            if (!validateEmail($email)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid email format']);
                exit;
            }

            $passwordValidation = validatePassword($password);
            if ($passwordValidation !== true) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => $passwordValidation]);
                exit;
            }

            if ($password !== $confirmPassword) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Passwords do not match']);
                exit;
            }

            // Check if user already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->fetch()) {
                http_response_code(409);
                echo json_encode(['success' => false, 'error' => 'Username or email already exists']);
                exit;
            }

            // Hash the password securely
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into the database
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $passwordHash])) {
                http_response_code(201);
                echo json_encode(['success' => true, 'message' => 'Registration successful!']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to register user']);
            }
            break;

        case 'login':
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';

            if (empty($username) || empty($password)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Username and password are required']);
                exit;
            }

            // Find user by username or email
            $stmt = $pdo->prepare("SELECT id, username, email, password_hash FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            // Verify password
            if ($user && password_verify($password, $user['password_hash'])) {
                $token = generateJWT($user['id'], $user['username'], $user['email']);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Login successful!', 
                    'token' => $token,
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email']
                    ]
                ]);
            } else {
                http_response_code(401);
                echo json_encode(['success' => false, 'error' => 'Invalid username or password']);
            }
            break;

        case 'logout':
            // With JWT, logout is handled client-side by removing the token
            echo json_encode(['success' => true, 'message' => 'Logout successful!']);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
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
