<?php
require_once '../config/Database.php';
session_start();

// Admin check
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

// Get student id from URL
$id = $_GET['id'] ?? 0;
$db = Database::connect();

// Fetch student + user data
$stmt = $db->prepare("
    SELECT student.id, student.class, student.phone, user.id AS user_id, user.name, user.email
    FROM student
    JOIN user ON student.user_id = user.id
    WHERE student.id = ?
");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$student){
    die("Student not found");
}

// Update student on POST
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Update user table
    $stmt1 = $db->prepare("UPDATE user SET name=?, email=? WHERE id=?");
    $stmt1->execute([
        $_POST['name'],
        $_POST['email'],
        $student['user_id']
    ]);

    // Update student table
    $stmt2 = $db->prepare("UPDATE student SET class=?, phone=? WHERE id=?");
    $stmt2->execute([
        $_POST['class'],
        $_POST['phone'],
        $id
    ]);

    // Redirect back to admin dashboard
    header("Location: admin_dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Student</title>
<link rel="stylesheet" href="../assets/style.css">
<style>
.container { max-width:500px; margin:50px auto; background:#fff0f5; padding:20px; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.1);}
input, button { width:100%; margin-bottom:10px; padding:8px; border-radius:6px; border:1px solid #ffb3d1; }
button { background-color:#ff3399; color:#fff; cursor:pointer; }
button:hover { background-color:#e62e8b; }
</style>
</head>
<body>
<div class="container">
<h2>Edit Student</h2>
<form method="post">
<input type="text" name="name" value="<?= htmlspecialchars($student['name']) ?>" required>
<input type="text" name="class" value="<?= htmlspecialchars($student['class']) ?>" required>
<input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
<input type="text" name="phone" value="<?= htmlspecialchars($student['phone']) ?>" required>
<button type="submit">Update</button>
</form>
</div>
</body>
</html>
