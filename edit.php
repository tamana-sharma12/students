<?php
require_once '../config/Database.php';
session_start();

// Admin check
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$db = Database::connect();

// Student ka data fetch karein
$stmt = $db->prepare("
    SELECT student.id, student.class, student.phone, user.id AS user_id, user.name, user.email
    FROM student
    JOIN user ON student.user_id = user.id
    WHERE student.id = ?
");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$student){
    die("Student not found!");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student | Professional SMS</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background: #fff5f8; font-family: 'Poppins', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .container { max-width:400px; width: 100%; background:#fff; padding:30px; border-radius:20px; box-shadow:0 10px 30px rgba(255, 133, 162, 0.15); border: 1px solid #ffdae3; }
        h2 { color: #ff5e84; text-align: center; margin-bottom: 20px; }
        label { font-size: 13px; color: #ff85a2; font-weight: 600; }
        input { width:100%; margin-bottom:15px; padding:12px; border-radius:10px; border:1px solid #ffdae3; box-sizing: border-box; outline: none; }
        input:focus { border-color: #ff5e84; }
        button { background-color:#ff85a2; color:#fff; border: none; padding: 12px; border-radius: 10px; width:100%; font-weight: bold; cursor:pointer; transition: 0.3s; }
        button:hover { background-color:#ff5e84; transform: translateY(-2px); }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #888; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Student Details</h2>
    <form id="editForm">
        <input type="hidden" name="id" value="<?= $student['id'] ?>">
        <input type="hidden" name="user_id" value="<?= $student['user_id'] ?>">

        <label>Full Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($student['name']) ?>" required>
        
        <label>Class</label>
        <input type="text" name="class" value="<?= htmlspecialchars($student['class']) ?>" required>
        
        <label>Email Address</label>
        <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
        
        <label>Phone Number</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($student['phone']) ?>" required>
        
        <button type="submit" id="updateBtn">Update Student Details</button>
    </form>
    <a href="admin_dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

<script>
document.getElementById('editForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('updateBtn');
    btn.innerText = "Updating...";
    btn.disabled = true;

    const formData = new FormData(e.target);

    try {
        // Vahi API use karenge jo humne Dashboard ke liye banayi thi
        const response = await fetch('../api/update_student_api.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status === 'success') {
            Swal.fire('Updated!', result.message, 'success').then(() => {
                window.location.href = 'admin_dashboard.php';
            });
        } else {
            Swal.fire('Error', result.message, 'error');
            btn.innerText = "Update Student Details";
            btn.disabled = false;
        }
    } catch (error) {
        Swal.fire('Error', 'Server error!', 'error');
        btn.innerText = "Update Student Details";
        btn.disabled = false;
    }
});
</script>
</body>
</html>