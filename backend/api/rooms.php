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
    $stmt = $conn->prepare("
        SELECT 
            r.position_x, 
            r.position_y, 
            u.email,
            (r.user_id = ? AND r.current_position = 1) as is_current
        FROM rooms r
        JOIN users u ON r.user_id = u.id
        ORDER BY r.position_x, r.position_y
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $rooms = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'rooms' => array_map(function($room) {
            return [
                'x' => (int)$room['position_x'],
                'y' => (int)$room['position_y'],
                'owner' => $room['email'],
                'isCurrent' => (bool)$room['is_current']
            ];
        }, $rooms)
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to get rooms']);
}
