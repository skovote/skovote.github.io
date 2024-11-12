<?php
session_start();
require_once '../config/db_connect.php';

// Basic authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: ./login.php');
    exit;
}

$query = "SELECT * FROM people ORDER BY votes DESC";
$result = $db->query($query);
$people = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Admin Panel</h1>
            <div class="user-info">
                Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
        <a href="add_person.php" class="btn">Add New Person</a>
        <a href="manage_voters.php" class="btn">Manage Voters</a>
        
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Votes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($people as $person): ?>
                <tr>
                    <td><img src="../<?= htmlspecialchars($person['image_url']) ?>" width="50"></td>
                    <td><?= htmlspecialchars($person['name']) ?></td>
                    <td><?= htmlspecialchars($person['age']) ?></td>
                    <td><?= htmlspecialchars($person['votes']) ?></td>
                    <td>
                        <a href="edit_person.php?id=<?= $person['id'] ?>" class="btn-small">Edit</a>
                        <button onclick="deletePerson(<?= $person['id'] ?>)" class="btn-small delete">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="../js/admin.js"></script>
</body>
</html> 