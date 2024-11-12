<?php
session_start();
require_once '../config/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? 0;
$error = '';
$success = '';

// Fetch person data
$stmt = $db->prepare("SELECT * FROM people WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$person = $stmt->get_result()->fetch_assoc();

if (!$person) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $age = (int)($_POST['age'] ?? 0);
    
    if (empty($name) || $age <= 0) {
        $error = 'Please fill all fields correctly';
    } else {
        $image_path = $person['image_url']; // Keep existing image by default
        
        // Handle new image upload if provided
        if (!empty($_FILES['image']['name'])) {
            $target_dir = "../uploads/";
            $image = $_FILES['image'];
            $new_image_path = $target_dir . uniqid() . '_' . basename($image['name']);
            
            if (move_uploaded_file($image['tmp_name'], $new_image_path)) {
                // Delete old image
                if (file_exists('../' . $person['image_url'])) {
                    unlink('../' . $person['image_url']);
                }
                $image_path = str_replace('../', '', $new_image_path);
            } else {
                $error = 'Failed to upload new image';
            }
        }
        
        if (empty($error)) {
            $stmt = $db->prepare("UPDATE people SET name = ?, age = ?, image_url = ? WHERE id = ?");
            $stmt->bind_param("sisi", $name, $age, $image_path, $id);
            
            if ($stmt->execute()) {
                $success = 'Person updated successfully!';
                $person['name'] = $name;
                $person['age'] = $age;
                $person['image_url'] = $image_path;
            } else {
                $error = 'Failed to update person';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Person</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Edit Person</h1>
        
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($person['name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" value="<?= htmlspecialchars($person['age']) ?>" required min="1">
            </div>
            
            <div class="form-group">
                <label>Current Image:</label>
                <img src="../<?= htmlspecialchars($person['image_url']) ?>" width="100">
            </div>
            
            <div class="form-group">
                <label for="image">New Image (optional):</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">Update Person</button>
                <a href="index.php" class="btn btn-secondary">Back to List</a>
            </div>
        </form>
    </div>
</body>
</html> 