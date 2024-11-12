<?php
session_start();
require_once '../config/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(['error' => 'Invalid ID']);
    exit;
}

$db->begin_transaction();

try {
    // Delete votes first
    $stmt = $db->prepare("DELETE FROM votes WHERE person_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Then delete person
    $stmt = $db->prepare("DELETE FROM people WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $db->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $db->rollback();
    echo json_encode(['error' => 'Failed to delete person']);
} 