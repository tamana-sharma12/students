<?php
session_start();
// Security: Check agar user login nahi hai
if (!isset($_SESSION['user_id'])) {
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
    <p>Welcome, <strong><?php echo $_SESSION['name'] ?? 'Student'; ?></strong>! Bas ye details bharte hi aapka dashboard taiyar ho jayega.</p>

    <form id="completeProfileForm">
        <div class="mb-3">
            <label class="form-label">Phone Number *</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                <input type="text" name="phone" class="form-control" placeholder="10 Digit Number" required maxlength="10">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Class / Course *</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                <input type="text" name="class" class="form-control" placeholder="e.g. B.Tech 2nd Year" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Gender *</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                <select name="gender" class="form-control" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
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
document.getElementById('completeProfileForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const finishBtn = document.querySelector('.btn-finish');

    // Button state change
    finishBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';
    finishBtn.disabled = true;

    try {
        // API Call
        const response = await fetch('../api/complete_profile_api.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Profile Completed!',
                text: result.message,
                confirmButtonColor: '#ff85a2'
            }).then(() => {
                window.location.href = 'student_dashboard.php';
            });
        } else {
            Swal.fire('Error', result.message, 'error');
            finishBtn.innerText = "Finish & Go to Dashboard";
            finishBtn.disabled = false;
        }
    } catch (error) {
        Swal.fire('Error', 'Server connection fail ho gaya!', 'error');
        finishBtn.disabled = false;
    }
});
</script>
</body>
</html>