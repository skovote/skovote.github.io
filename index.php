<?php
session_start();
require_once 'config/db_connect.php';

// Set or get device ID
if (!isset($_COOKIE['device_id'])) {
    $device_id = hash('sha256', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . uniqid());
    setcookie('device_id', $device_id, time() + (30 * 24 * 60 * 60), '/', '', true, true);
} else {
    $device_id = $_COOKIE['device_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting System</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div id="voting-area">
            <div class="person-container" id="person1">
                <!-- Will be populated by JavaScript -->
            </div>
            <div class="person-container" id="person2">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>
    </div>
    <script src="js/vote.js"></script>
</body>
</html> 