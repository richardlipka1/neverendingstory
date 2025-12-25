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

require_once '../Database.php';

$db = new Database();
$conn = $db->connect();

try {
    $stmt = $conn->prepare("SELECT position_x, position_y FROM rooms WHERE user_id = ? AND current_position = 1");
    $stmt->execute([$_SESSION['user_id']]);
    $position = $stmt->fetch();
    
    if (!$position) {
        http_response_code(404);
        echo json_encode(['error' => 'Position not found']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'position' => [
            'x' => (int)$position['position_x'],
            'y' => (int)$position['position_y']
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to get position']);
}
