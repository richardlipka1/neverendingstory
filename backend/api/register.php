<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Email and password are required']);
    exit;
}

$email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
if (!$email) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}

$password = $data['password'];
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must be at least 6 characters']);
    exit;
}

$db = new Database();
$conn = $db->connect();

try {
    // Check if user already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'User already exists']);
        exit;
    }
    
    // Get the next available position
    $stmt = $conn->prepare("SELECT MAX(position_x) as max_x, MAX(position_y) as max_y FROM rooms");
    $stmt->execute();
    $result = $stmt->fetch();
    $nextX = ($result['max_x'] ?? -1) + 1;
    $nextY = ($result['max_y'] ?? -1) + 1;
    
    // Create user
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
    $stmt->execute([$email, $passwordHash]);
    $userId = $conn->lastInsertId();
    
    // Create user's room
    $stmt = $conn->prepare("INSERT INTO rooms (user_id, position_x, position_y, current_position) VALUES (?, ?, ?, 1)");
    $stmt->execute([$userId, $nextX, $nextY]);
    
    $_SESSION['user_id'] = $userId;
    $_SESSION['email'] = $email;
    
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $userId,
            'email' => $email
        ],
        'position' => [
            'x' => $nextX,
            'y' => $nextY
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Registration failed']);
}
