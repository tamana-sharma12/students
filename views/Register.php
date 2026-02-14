<?php
session_start();
$message = ""; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Pink SMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #fff0f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Poppins', sans-serif; }
        .register-card { background: #fff; padding: 35px; border-radius: 20px; box-shadow: 0 10px 30px rgba(255, 133, 162, 0.2); border: 1px solid #ffdae3; width: 100%; max-width: 420px; }
        .register-card h3 { color: #ff85a2; font-weight: 700; text-align: center; margin-bottom: 25px; }
        .form-label { color: #ff5e84; font-weight: 600; font-size: 0.85rem; }
        .form-control { border-radius: 10px; border: 1.5px solid #ffdae3; padding: 10px 15px; }
        .btn-register { background: #ff85a2; color: white; border: none; padding: 12px; border-radius: 10px; width: 100%; font-weight: 600; margin-top: 15px; transition: 0.3s; }
        .btn-register:hover { background: #ff5e84; transform: translateY(-2px); }
        .login-footer { text-align: center; margin-top: 20px; font-size: 0.9rem; color: #666; }
        .login-footer a { color: #ff85a2; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>

<div class="register-card">
    <h3><i class="fas fa-user-plus me-2"></i>Register</h3>

    <?php if($message): ?>
        <div class="alert alert-danger py-2" style="font-size: 0.8rem;"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0" style="color: #ff85a2;"><i class="fas fa-user"></i></span>
                <input type="text" name="name" class="form-control border-start-0" placeholder="Enter your name" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0" style="color: #ff85a2;"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" class="form-control border-start-0" placeholder="email@example.com" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0" style="color: #ff85a2;"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
            </div>
        </div>

        <button type="submit" name="create_account" class="btn-register">
            Create Account & Send OTP <i class="fas fa-arrow-right ms-2"></i>
        </button>
    </form>

    <div class="login-footer">
        Already have an account? <a href="student_login.php">Login here</a>
    </div>
</div>

<script>
const registerForm = document.querySelector('form');

registerForm.addEventListener('submit', async (e) => {
    e.preventDefault(); 
    
    const submitBtn = registerForm.querySelector('button');
    submitBtn.disabled = true;
    submitBtn.innerHTML = "Processing...";

    const formData = new FormData(registerForm);

    try {
        // '../' isliye kyunki register.php 'views' folder mein hai
        // Ise karne se ye seedha 'api' folder mein pahunchega
        const response = await fetch('../api/register_api.php', {
            method: 'POST',
            body: formData
        });

        // Backend ka response check karne ke liye
        const responseText = await response.text();
        console.log("Server Ka Response:", responseText); 

        try {
            const result = JSON.parse(responseText);
            if (result.status === 'success') {
                alert("Success! OTP is: " + result.otp); 
                window.location.href = 'verify_otp.php';
            } else {
                alert("Error: " + result.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Create Account & Send OTP';
            }
        } catch (jsonError) {
            console.error("JSON Error:", jsonError);
            alert("PHP file mein error hai! Network tab (F12) check karein.");
            submitBtn.disabled = false;
        }

    } catch (error) {
        console.error("Network Error:", error);
        alert("Rasta (Path) galat hai! API folder nahi mil raha.");
        submitBtn.disabled = false;
    }
});
</script>
</body>
</html>