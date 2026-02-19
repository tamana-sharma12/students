<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Aapka original CSS */
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}
        :root{ --pink:#ff85a2; --pink-dark:#ff6f92; }
        body{ min-height:100vh; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg,#ffe6ee,#ffd1dc); }
        .card{ width:380px; padding:40px; border-radius:26px; background:white; box-shadow:0 25px 50px rgba(255,133,162,0.25); text-align:center; }
        .icon{ width:72px; height:72px; border-radius:50%; background:var(--pink); color:white; display:flex; align-items:center; justify-content:center; font-size:30px; margin:0 auto 20px; }
        h2{ color:#333; margin-bottom:20px; }
        input{ width:100%; padding:14px; border-radius:14px; border:2px solid #f1f1f1; margin-bottom:20px; outline:none; }
        input:focus{ border-color: var(--pink); }
        button{ width:100%; padding:14px; border:none; border-radius:14px; background:var(--pink); color:white; font-size:16px; font-weight:600; cursor:pointer; transition: 0.3s; }
        button:hover{ background:var(--pink-dark); transform: translateY(-2px); }
        .login-btn-container { display: none; margin-top: 15px; }
        .login-btn{ display:block; padding:12px; border-radius:14px; background:#fff; border:2px solid var(--pink); color:var(--pink); font-weight:600; text-decoration:none; transition: 0.3s; }
        .login-btn:hover{ background:var(--pink); color:#fff; }
    </style>
</head>

<body>
<div class="card">
    <div class="icon">🔑</div>
    <h2>Reset Password</h2>

    <form id="resetForm">
        <input type="hidden" id="userEmail" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
        
        <input type="password" id="newPassword" placeholder="New password" required>
        <input type="password" id="confirmPassword" placeholder="Confirm new password" required>
        
        <button type="submit" id="updateBtn">Update Password</button>
    </form>

    <div class="login-btn-container" id="successArea">
        <p style="color: green; margin-bottom: 10px;">✔ Password updated successfully</p>
        <a href="student_login.php" class="login-btn">Go to Login</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#resetForm').on('submit', function(e) {
        e.preventDefault();

        let email = $('#userEmail').val();
        let password = $('#newPassword').val();
        let confirm = $('#confirmPassword').val();

        // Basic Validation
        if(password !== confirm) {
            Swal.fire('Oops!', 'Passwords match nahi kar rahe!', 'error');
            return;
        }

        $('#updateBtn').text('Updating...').prop('disabled', true);

        // Webservice Call via AJAX
        $.ajax({
            url: '../api/reset_password_api.php',
            type: 'POST',
            data: { email: email, password: password },
            dataType: 'json',
            success: function(res) {
                if(res.status === 'success') {
                    Swal.fire('Success!', res.message, 'success');
                    $('#resetForm').hide();
                    $('#successArea').fadeIn();
                } else {
                    Swal.fire('Error!', res.message, 'error');
                    $('#updateBtn').text('Update Password').prop('disabled', false);
                }
            },
            error: function() {
                Swal.fire('Error!', 'Server connection error!', 'error');
                $('#updateBtn').text('Update Password').prop('disabled', false);
            }
        });
    });
});
</script>
</body>
</html>