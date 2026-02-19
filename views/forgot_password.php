<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Aapka purana CSS bilkul waisa hi rahega */
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}
        :root{ --pink:#ff85a2; --pink-dark:#ff6f92; }
        body{ min-height:100vh; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg,#ffe6ee,#ffd1dc); }
        .card{ width:380px; padding:40px; border-radius:26px; background: rgba(255,255,255,0.9); box-shadow:0 25px 50px rgba(255,133,162,0.25); text-align:center; }
        .icon{ width:72px; height:72px; border-radius:50%; background:var(--pink); color:white; display:flex; align-items:center; justify-content:center; font-size:30px; margin:0 auto 20px; }
        h2{ color:#333; margin-bottom:8px; font-weight:600; }
        p{ color:#666; font-size:14px; margin-bottom:25px; }
        input{ width:100%; padding:14px 16px; border-radius:14px; border:2px solid #f1f1f1; margin-bottom:20px; outline:none; transition:.3s; }
        input:focus{ border-color:var(--pink); }
        button{ width:100%; padding:14px; border:none; border-radius:14px; background:var(--pink); color:white; font-size:16px; font-weight:600; cursor:pointer; transition:.3s; }
        button:hover{ background:var(--pink-dark); transform:translateY(-2px); box-shadow:0 10px 25px rgba(255,133,162,0.5); }
        .success-box{ margin-top:18px; color:#2e7d32; background: #e8f5e9; padding: 10px; border-radius: 10px; display:none; }
        a{ color:var(--pink); font-weight:600; text-decoration:none; }
    </style>
</head>

<body>
<div class="card">
    <div class="icon">🔐</div>
    <h2>Forgot Password</h2>
    <p>Enter your registered email to reset password</p>

    <form id="forgotForm">
        <input type="email" id="email" name="email" placeholder="Email address" required>
        <button type="submit" id="submitBtn">Send Reset Link</button>
    </form>

    <div id="result" class="success-box"></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('#forgotForm').on('submit', function(e) {
        e.preventDefault(); // Page refresh roko
        
        let email = $('#email').val();
        $('#submitBtn').text('Checking...').prop('disabled', true);

        // AJAX Call (Webservice)
        $.ajax({
            url: '../api/forgot_password_api.php',
            type: 'POST',
            data: { email: email },
            dataType: 'json',
            success: function(res) {
                $('#submitBtn').text('Send Reset Link').prop('disabled', false);
                
                if (res.status === 'success') {
                    Swal.fire('Success!', res.message, 'success');
                    $('#result').html(
                        `Reset link generated ✔<br><a href="${res.link}">Click here to Reset Password</a>`
                    ).fadeIn();
                } else {
                    Swal.fire('Error!', res.message, 'error');
                }
            },
            error: function() {
                $('#submitBtn').text('Send Reset Link').prop('disabled', false);
                Swal.fire('Error!', 'Server se connect nahi ho paya!', 'error');
            }
        });
    });
});
</script>
</body>
</html>