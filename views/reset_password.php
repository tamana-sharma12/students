<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../config/database.php";
$conn = Database::connect();

$email = $_GET['email'] ?? '';
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pass = $_POST['password'];

    $stmt = $conn->prepare("UPDATE user SET password=? WHERE email=?");
    $stmt->execute([$pass, $email]);

    $success = true;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Reset Password</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}

:root{
    --pink:#ff85a2;
    --pink-dark:#ff6f92;
}

body{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    background: linear-gradient(135deg,#ffe6ee,#ffd1dc);
}

.card{
    width:380px;
    padding:40px;
    border-radius:26px;
    background:white;
    box-shadow:0 25px 50px rgba(255,133,162,0.25);
    text-align:center;
}

.icon{
    width:72px;
    height:72px;
    border-radius:50%;
    background:var(--pink);
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:30px;
    margin:0 auto 20px;
}

h2{
    color:#333;
    margin-bottom:20px;
}

input{
    width:100%;
    padding:14px;
    border-radius:14px;
    border:2px solid #f1f1f1;
    margin-bottom:20px;
}

button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:14px;
    background:var(--pink);
    color:white;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
}

button:hover{
    background:var(--pink-dark);
}

.success{
    color:#2e7d32;
    font-weight:600;
    margin-bottom:15px;
}

.login-btn{
    display:block;
    margin-top:10px;
    padding:12px;
    border-radius:14px;
    background:#fff;
    border:2px solid var(--pink);
    color:var(--pink);
    font-weight:600;
    text-decoration:none;
}
.login-btn:hover{
    background:var(--pink);
    color:#fff;
}
</style>
</head>

<body>
<div class="card">
    <div class="icon">🔑</div>
    <h2>Reset Password</h2>

    <?php if(!$success): ?>
        <form method="post">
            <input type="password" name="password" placeholder="New password" required>
            <button>Update Password</button>
        </form>
    <?php else: ?>
        <div class="success">✔ Password updated successfully</div>
        <a href="student_login.php" class="login-btn">Go to Login</a>
    <?php endif; ?>

</div>
</body>
</html>
