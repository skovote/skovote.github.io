<?php
session_start();
require_once '../config/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$query = "SELECT cs.*, a.username as admin_name 
          FROM completed_sessions cs 
          LEFT JOIN admins a ON cs.updated_by = a.id 
          ORDER BY cs.completed_at DESC";
$result = $db->query($query);
$sessions = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Voters - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Manage Voters</h1>
            <div class="user-info">
                Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?>
                <a href="index.php" class="btn-secondary">Back to Dashboard</a>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Device ID</th>
                    <th>Completed At</th>
                    <th>Revote Status</th>
                    <th>Last Updated By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sessions as $session): ?>
                <tr>
                    <td><?= substr(htmlspecialchars($session['device_id']), 0, 16) ?>...</td>
                    <td><?= htmlspecialchars($session['completed_at']) ?></td>
                    <td><?= $session['revote_used'] ? 'Used' : 'Available' ?></td>
                    <td><?= htmlspecialchars($session['admin_name'] ?? 'N/A') ?></td>
                    <td>
                        <button 
                            onclick="toggleRevote('<?= $session['device_id'] ?>')"
                            class="btn-small <?= $session['revote_used'] ? 'allow' : 'revoke' ?>"
                        >
                            <?= $session['revote_used'] ? 'Allow Revote' : 'Revoke Access' ?>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="../js/manage_voters.js"></script>
</body>
</html> 