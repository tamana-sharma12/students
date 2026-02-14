<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: student_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile | SMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #fff0f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Poppins', sans-serif; }
        .profile-card { background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(255, 133, 162, 0.2); border: 1px solid #ffdae3; max-width: 450px; width: 100%; }
        .profile-card h3 { color: #ff85a2; font-weight: 700; margin-bottom: 10px; text-align: center; }
        .profile-card p { color: #777; font-size: 0.9rem; text-align: center; margin-bottom: 30px; }
        .form-label { color: #ff5e84; font-weight: 600; font-size: 0.85rem; }
        .form-control { border-radius: 10px; border: 1px solid #ffdae3; padding: 12px; }
        .form-control:focus { border-color: #ff85a2; box-shadow: 0 0 0 0.25rem rgba(255, 133, 162, 0.15); }
        .btn-finish { background: #ff85a2; color: white; border: none; padding: 12px; border-radius: 10px; width: 100%; font-weight: 600; margin-top: 20px; transition: 0.3s; }
        .btn-finish:hover { background: #ff5e84; transform: translateY(-2px); }
        .input-group-text { background: #fff9fa; border-color: #ffdae3; color: #ff85a2; }
    </style>
</head>
<body>

<div class="profile-card">
    <h3>Complete Your Profile</h3>
    <p>Welcome, <strong><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Student'; ?></strong>! Just a few more details to get you started.</p>

    <?php if($message): ?>
        <div class="alert alert-danger py-2" style="font-size: 0.8rem;"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Phone Number *</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                <input type="text" name="phone" class="form-control" placeholder="e.g. +91 9876543210" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Class / Course *</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                <input type="text" name="class" class="form-control" placeholder="e.g. B.Tech CS" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Full Address</label>
            <textarea name="address" class="form-control" rows="3" placeholder="Enter your full address"></textarea>
        </div>

        <button type="submit" class="btn btn-finish">
            Finish & Go to Dashboard <i class="fas fa-arrow-right ms-2"></i>
        </button>
    </form>
</div>
<script>
document.querySelector('form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const finishBtn = document.querySelector('.btn-finish');

    finishBtn.innerText = "Saving Details...";
    finishBtn.disabled = true;

    try {
        const response = await fetch('../api/update_profile_api.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status === 'success') {
            alert(result.message);
            window.location.href = 'student_dashboard.php';
        } else {
            alert("Error: " + result.message);
            finishBtn.innerText = "Finish & Go to Dashboard";
            finishBtn.disabled = false;
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Server se connection nahi ho paya!");
        finishBtn.disabled = false;
    }
});
</script>
</body>
</html>