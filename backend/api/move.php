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

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

require_once '../Database.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['direction'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Direction is required']);
    exit;
}

$direction = strtolower($data['direction']);
$validDirections = ['up', 'down', 'left', 'right'];

if (!in_array($direction, $validDirections)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid direction. Use: up, down, left, right']);
    exit;
}

$db = new Database();
$conn = $db->connect();

try {
    // Get current position
    $stmt = $conn->prepare("SELECT position_x, position_y FROM rooms WHERE user_id = ? AND current_position = 1");
    $stmt->execute([$_SESSION['user_id']]);
    $currentPos = $stmt->fetch();
    
    if (!$currentPos) {
        http_response_code(404);
        echo json_encode(['error' => 'Current position not found']);
        exit;
    }
    
    $currentX = (int)$currentPos['position_x'];
    $currentY = (int)$currentPos['position_y'];
    
    // Calculate new position
    $newX = $currentX;
    $newY = $currentY;
    
    switch ($direction) {
        case 'up':
            $newY--;
            break;
        case 'down':
            $newY++;
            break;
        case 'left':
            $newX--;
            break;
        case 'right':
            $newX++;
            break;
    }
    
    // Check if room exists at new position
    $stmt = $conn->prepare("SELECT id FROM rooms WHERE position_x = ? AND position_y = ?");
    $stmt->execute([$newX, $newY]);
    $roomExists = $stmt->fetch();
    
    // Start transaction
    $conn->beginTransaction();
    
    // If room doesn't exist, create it
    if (!$roomExists) {
        $stmt = $conn->prepare("INSERT INTO rooms (user_id, position_x, position_y, current_position) VALUES (?, ?, ?, 0)");
        $stmt->execute([$_SESSION['user_id'], $newX, $newY]);
    }
    
    // Update current position
    $stmt = $conn->prepare("UPDATE rooms SET current_position = 0 WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    $stmt = $conn->prepare("UPDATE rooms SET current_position = 1 WHERE user_id = ? AND position_x = ? AND position_y = ?");
    $stmt->execute([$_SESSION['user_id'], $newX, $newY]);
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'position' => [
            'x' => $newX,
            'y' => $newY
        ],
        'message' => "Moved $direction"
    ]);
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    http_response_code(500);
    echo json_encode(['error' => 'Failed to move']);
}
