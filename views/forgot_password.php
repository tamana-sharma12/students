<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../config/database.php";
$conn = Database::connect();

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT id FROM user WHERE email=?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $msg = "<div class='success'>
        Reset link generated ✔<br>
        <a href='reset_password.php?email=$email'>Reset Password</a>
        </div>";
    } else {
        $msg = "<div class='error'>Email not found ❌</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>
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
    background: rgba(255,255,255,0.9);
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
    margin-bottom:8px;
    font-weight:600;
}

p{
    color:#666;
    font-size:14px;
    margin-bottom:25px;
}

input{
    width:100%;
    padding:14px 16px;
    border-radius:14px;
    border:2px solid #f1f1f1;
    margin-bottom:20px;
    outline:none;
    transition:.3s;
}

input:focus{
    border-color:var(--pink);
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
    transition:.3s;
}

button:hover{
    background:var(--pink-dark);
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(255,133,162,0.5);
}

.success{
    margin-top:18px;
    color:#2e7d32;
}

.error{
    margin-top:18px;
    color:#c62828;
}

a{
    color:var(--pink);
    font-weight:600;
    text-decoration:none;
}
</style>
</head>

<body>
<div class="card">
    <div class="icon">🔐</div>
    <h2>Forgot Password</h2>
    <p>Enter your registered email to reset password</p>

    <form method="post">
        <input type="email" name="email" placeholder="Email address" required>
        <button>Send Reset Link</button>
    </form>

    <?php echo $msg; ?>
</div>
</body>
</html>
