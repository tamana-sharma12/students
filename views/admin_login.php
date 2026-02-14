<?php
session_start();
$error = "";
if (!isset($_SESSION['captcha_ans'])) {
    $num1 = rand(1, 9); $num2 = rand(1, 9);
    $_SESSION['captcha_ans'] = $num1 + $num2;
    $_SESSION['captcha_text'] = "$num1 + $num2";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | SMS Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { margin: 0; padding: 0; font-family: sans-serif; background: #ffe4ea; height: 100vh; display: flex; justify-content: center; align-items: center; }
        .login-box { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 25px rgba(255,133,162,0.2); width: 350px; text-align: center; border: 1px solid #ffdae3; }
        .input-field { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ffdae3; border-radius: 10px; outline: none; box-sizing: border-box; background: #fff9fa; }
        .captcha-box { background: #ffdae3; color: #ff5e84; padding: 10px; border-radius: 8px; font-weight: bold; margin-bottom: 10px; display: inline-block; width: 100%; }
        .login-btn { width: 100%; padding: 12px; background: #ff85a2; border: none; color: white; border-radius: 10px; font-weight: 600; cursor: pointer; }
        .error-msg { color: #ff4d4d; font-size: 13px; margin-bottom: 15px; background: #fff0f3; padding: 10px; border-radius: 8px; border: 1px solid #ffcdd2; }
    </style>
</head>
<body>

<div class="login-box">
    <h2 style="color: #ff85a2;">Admin Login</h2>
    <?php if($error): ?><div class="error-msg"><?= $error ?></div><?php endif; ?>

    <form method="POST">
        <input type="email" name="email" class="input-field" placeholder="Admin Email" required>
        <input type="password" name="password" class="input-field" placeholder="Password" required maxlength="8">
        
        <div class="captcha-box">Verify: <?= $_SESSION['captcha_text'] ?> = ?</div>
        <input type="number" name="captcha_input" class="input-field" placeholder="Enter Answer" required>

        <button type="submit" class="login-btn">Secure Login</button>
    </form>
    <a href="../index.php" style="display:block; margin-top:20px; color:#ff85a2; text-decoration:none; font-size:13px;">← Back to Home</a>
</div>
<script>
document.querySelector('form').addEventListener('submit', async (e) => {
    e.preventDefault(); // Page reload rokne ke liye
    
    const formData = new FormData(e.target);
    const captchaBox = document.querySelector('.captcha-box');
    const loginBtn = document.querySelector('.login-btn');

    loginBtn.innerText = "Verifying...";
    loginBtn.disabled = true;

    try {
        const response = await fetch('../api/admin_login_api.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status === 'success') {
            window.location.href = 'admin_dashboard.php';
        } else {
            alert(result.message);
            // Agar captcha galat hai, toh naya captcha show karein
            if(result.new_captcha) {
                captchaBox.innerText = "Verify: " + result.new_captcha + " = ?";
            }
            loginBtn.innerText = "Secure Login";
            loginBtn.disabled = false;
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Server connection failed!");
        loginBtn.innerText = "Secure Login";
        loginBtn.disabled = false;
    }
});
</script>
</body>
</html>