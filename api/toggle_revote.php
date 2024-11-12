<?php
session_start();
require_once '../config/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$device_id = $data['device_id'] ?? null;

if (!$device_id) {
    echo json_encode(['error' => 'Invalid device ID']);
    exit;
}

try {
    // Toggle the revote_used status
    $stmt = $db->prepare("UPDATE completed_sessions SET revote_used = NOT revote_used, updated_by = ? WHERE device_id = ?");
    $stmt->bind_param("is", $_SESSION['admin_id'], $device_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception($db->error);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to update revote permission']);
} 