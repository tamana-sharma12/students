<?php
session_start();

// Google URL config (Same as before)
$google_url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
    'client_id' => "682449346419-aqreg5epb9pkqp07q7imc1fqf4cfr8ot.apps.googleusercontent.com",
    'redirect_uri' => "http://localhost/students/google-callback.php",
    'response_type' => 'code',
    'scope' => 'email profile',
    'prompt' => 'select_account'
]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login | ScholarTrack</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-pink: #ff85a2;
            --dark-pink: #ff5e84;
            --soft-pink: #fff0f5;
            --glass-bg: rgba(255, 255, 255, 0.9);
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #fff0f5 0%, #ffe4e1 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            padding: 50px 40px;
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(255, 133, 162, 0.15);
            width: 100%;
            max-width: 420px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: transform 0.3s ease;
        }

        .brand-logo {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-pink);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            background: var(--soft-pink);
            color: var(--primary-pink);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 20px;
            border: 3px solid #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        h2 {
            color: #444;
            font-weight: 600;
            margin-bottom: 30px;
            font-size: 22px;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-pink);
        }

        input {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border: 2px solid #f0f0f0;
            border-radius: 15px;
            box-sizing: border-box;
            outline: none;
            transition: all 0.3s ease;
            font-size: 15px;
            background: #fdfdfd;
        }

        input:focus {
            border-color: var(--primary-pink);
            background: #fff;
            box-shadow: 0 0 10px rgba(255, 133, 162, 0.1);
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: var(--primary-pink);
            color: white;
            border: none;
            border-radius: 15px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(255, 133, 162, 0.2);
            margin-top: 10px;
        }

        .login-btn:hover {
            background: var(--dark-pink);
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(255, 133, 162, 0.3);
        }

        .divider {
            margin: 30px 0;
            display: flex;
            align-items: center;
            color: #bbb;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .divider::before, .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #eee;
            margin: 0 15px;
        }

        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            text-decoration: none;
            color: #555;
            border: 2px solid #f0f0f0;
            padding: 12px;
            border-radius: 15px;
            transition: all 0.3s ease;
            font-weight: 500;
            background: #fff;
        }

        .google-btn:hover {
            background: #f9f9f9;
            border-color: #ddd;
        }

        .google-btn img {
            width: 22px;
        }

        .footer-links {
            margin-top: 30px;
            font-size: 14px;
            color: #888;
        }

        .footer-links a {
            color: var(--primary-pink);
            text-decoration: none;
            font-weight: 600;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .alert-error {
            background: #fff5f5;
            color: #e74c3c;
            padding: 12px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 20px;
            border: 1px solid #ffebeb;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="brand-logo">
        <i class="fas fa-graduation-cap"></i> ScholarTrack
    </div>

    <div class="user-avatar">
        <i class="fas fa-user"></i>
    </div>
    
    <h2>Welcome Back!</h2>

    <?php if(isset($error) && $error): ?>
        <div class="alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" placeholder="Email Address" required>
        </div>
        
        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <button type="submit" class="login-btn">Login to Dashboard</button>
    </form>
    

<div style="text-align:right; margin-top:10px;">
    <a href="forgot_password.php" 
       style="font-size:14px; color:#ff5e84; font-weight:600; text-decoration:none;">
        Forgot Password?
    </a>
</div>



    <div class="divider">OR</div>

    <a href="<?= $google_url ?>" class="google-btn">
        <img src="https://www.gstatic.com/images/branding/product/1x/googleg_48dp.png" alt="Google">
        Continue with Google
    </a>

    <div class="footer-links">
        Don't have an account? <a href="register.php">Register Now</a><br><br>
        <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>
</div>
<script>
document.querySelector('form').addEventListener('submit', async (e) => {
    e.preventDefault(); // Page refresh hone se rokne ke liye
    
    const formData = new FormData(e.target);
    const loginBtn = document.querySelector('.login-btn');
    const originalBtnText = loginBtn.innerText;
    
    loginBtn.innerText = "Authenticating..."; // Loading state dikhane ke liye
    loginBtn.disabled = true;

    try {
        // Webservice (API) ko call karna
        const response = await fetch('../api/login_api.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status === 'success') {
            // Success hone par dashboard par bhejein
            window.location.href = 'student_dashboard.php';
        } else {
            // Error hone par alert dikhayein
            alert("Login Failed: " + result.message);
            loginBtn.innerText = originalBtnText;
            loginBtn.disabled = false;
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Server se connection nahi ho paya!");
        loginBtn.innerText = originalBtnText;
        loginBtn.disabled = false;
    }
});
</script>
</body>
</html>