<?php
session_start();
if (!isset($_SESSION['verify_email'])) {
    header("Location: register.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP | Pink SMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #fff0f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Poppins', sans-serif; }
        .otp-card { background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(255, 133, 162, 0.2); border: 1px solid #ffdae3; width: 100%; max-width: 400px; text-align: center; }
        .otp-card h3 { color: #ff85a2; font-weight: 700; margin-bottom: 10px; }
        .otp-card p { color: #777; font-size: 0.9rem; margin-bottom: 25px; }
        .form-control { border-radius: 12px; border: 2px solid #ffdae3; padding: 15px; text-align: center; font-size: 1.5rem; letter-spacing: 10px; font-weight: bold; color: #ff5e84; }
        .btn-verify { background: #ff85a2; color: white; border: none; padding: 12px; border-radius: 10px; width: 100%; font-weight: 600; transition: 0.3s; margin-top: 20px; }
        .btn-verify:hover { background: #ff5e84; transform: translateY(-2px); }
    </style>
</head>
<body>

<div class="otp-card">
    <i class="fas fa-shield-alt fa-3x mb-3" style="color: #ff85a2;"></i>
    <h3>Email Verification</h3>
    <p>We've sent a 6-digit code to <b><?php echo $_SESSION['verify_email']; ?></b></p>

    <form id="verifyForm">
        <div class="mb-3">
            <input type="text" name="otp" id="otpInput" class="form-control" placeholder="000000" maxlength="6" required>
        </div>
        <button type="submit" class="btn-verify">
            Verify & Activate Account <i class="fas fa-check-circle ms-2"></i>
        </button>
    </form>
</div>

<script>
const verifyForm = document.getElementById('verifyForm');

verifyForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const submitBtn = verifyForm.querySelector('button');
    submitBtn.disabled = true;
    submitBtn.innerHTML = "Verifying...";

    const formData = new FormData(verifyForm);

    try {
        // Path corrected: views folder se bahar nikal kar api folder mein jana
        const response = await fetch('../api/verify_otp_api.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status === 'success') {
            alert(result.message);
            window.location.href = 'student_login.php?status=verified';
        } else {
            alert("Error: " + result.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Verify & Activate Account <i class="fas fa-check-circle ms-2"></i>';
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Server connection failed!");
        submitBtn.disabled = false;
    }
});
</script>
</body>
</html>