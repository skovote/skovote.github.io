<?php
session_start();
require_once '../config/db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$person_id = $data['person_id'] ?? null;
$other_person_id = $data['other_person_id'] ?? null;
$device_id = $_COOKIE['device_id'] ?? null;
$session_id = $_COOKIE['voting_session'] ?? null;

if (!$person_id || !$device_id || !$other_person_id) {
    echo json_encode(['error' => 'Missing required data']);
    exit;
}

// Create new session if doesn't exist
if (!$session_id) {
    $session_id = uniqid('session_', true);
    setcookie('voting_session', $session_id, time() + (30 * 24 * 60 * 60), '/', '', true, true);
}

$db->begin_transaction();

try {
    // Update votes count for the voted person
    $update_query = "UPDATE people SET votes = votes + 1 WHERE id = ?";
    $stmt = $db->prepare($update_query);
    $stmt->bind_param("i", $person_id);
    $stmt->execute();

    // Record the vote and track the non-voted person
    $vote_query = "INSERT INTO votes (device_id, person_id, other_person_id, session_id, voted_at) 
                   VALUES (?, ?, ?, ?, NOW())";
    $stmt = $db->prepare($vote_query);
    $stmt->bind_param("siis", $device_id, $person_id, $other_person_id, $session_id);
    $stmt->execute();

    // Check remaining unvoted people
    $check_remaining = "SELECT COUNT(*) as remaining FROM people 
                       WHERE id NOT IN (
                           SELECT person_id FROM votes 
                           WHERE device_id = ? AND session_id = ?
                       )
                       AND id NOT IN (
                           SELECT other_person_id FROM votes 
                           WHERE device_id = ? AND session_id = ?
                       )";
    $stmt = $db->prepare($check_remaining);
    $stmt->bind_param("ssss", $device_id, $session_id, $device_id, $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $remaining = $result->fetch_assoc()['remaining'];

    if ($remaining < 2) {
        // Mark session as complete
        $complete_query = "INSERT INTO completed_sessions (device_id, revote_used) VALUES (?, FALSE)
                          ON DUPLICATE KEY UPDATE revote_used = FALSE";
        $stmt = $db->prepare($complete_query);
        $stmt->bind_param("s", $device_id);
        $stmt->execute();
    }

    $db->commit();
    echo json_encode(['success' => true, 'remaining' => $remaining]);
} catch (Exception $e) {
    $db->rollback();
    echo json_encode(['error' => 'Failed to record vote']);
}
?> 