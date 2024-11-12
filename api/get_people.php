<?php
require_once '../config/db_connect.php';

header('Content-Type: application/json');

$device_id = $_COOKIE['device_id'] ?? null;

if (!$device_id) {
    echo json_encode(['error' => 'No device ID']);
    exit;
}

// Check if device has completed a session before
$check_query = "SELECT revote_used FROM completed_sessions WHERE device_id = ?";
$stmt = $db->prepare($check_query);
$stmt->bind_param("s", $device_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $session = $result->fetch_assoc();
    if (!$session['revote_used']) {
        echo json_encode(['previouslyCompleted' => true]);
        exit;
    }
}

$session_id = $_COOKIE['voting_session'] ?? null;

// Get people not voted on in current session
$query = "SELECT id, name, age, image_url FROM people 
          WHERE id NOT IN (
              SELECT person_id FROM votes 
              WHERE device_id = ? AND session_id = ?
          )
          AND id NOT IN (
              SELECT other_person_id FROM votes 
              WHERE device_id = ? AND session_id = ?
          )
          ORDER BY RAND() 
          LIMIT 2";

$stmt = $db->prepare($query);
$stmt->bind_param("ssss", $device_id, $session_id, $device_id, $session_id);
$stmt->execute();
$result = $stmt->get_result();
$people = $result->fetch_all(MYSQLI_ASSOC);

if (count($people) < 2) {
    echo json_encode(['completed' => true]);
} else {
    echo json_encode($people);
}
?> 