<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScholarTrack | Student Management Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-pink: #ff3d7f;
            --pink-light: #ff85a2;
            --pink-lighter: #ffebf0;
            --pink-gradient: linear-gradient(135deg, #ff3d7f 0%, #ff6b9d 100%);
            --dark-bg: #0f172a;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --white: #ffffff;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --shadow: 0 10px 30px rgba(255, 61, 127, 0.15);
            --border-radius: 20px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 50%, #fbcfe8 100%);
            color: var(--text-dark);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(255, 133, 162, 0.1) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(255, 61, 127, 0.1) 0%, transparent 20%),
                radial-gradient(circle at 50% 50%, rgba(255, 235, 240, 0.2) 0%, transparent 30%);
            z-index: -1;
        }

        .floating-element {
            position: absolute; border-radius: 50%; background: rgba(255, 133, 162, 0.1);
            z-index: -1; animation: float 20s infinite ease-in-out;
        }

        .floating-element:nth-child(1) { width: 300px; height: 300px; top: 10%; left: 5%; animation-delay: 0s; }
        .floating-element:nth-child(2) { width: 200px; height: 200px; top: 60%; right: 10%; animation-delay: 5s; }
        .floating-element:nth-child(3) { width: 150px; height: 150px; bottom: 10%; left: 15%; animation-delay: 10s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            33% { transform: translateY(-30px) rotate(120deg); }
            66% { transform: translateY(20px) rotate(240deg); }
        }

        nav {
            display: flex; justify-content: space-between; align-items: center;
            padding: 1.5rem 8%; background: var(--glass-bg); backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 133, 162, 0.2); position: fixed;
            width: 100%; top: 0; z-index: 1000; box-shadow: 0 5px 20px rgba(255, 61, 127, 0.1);
        }

        .logo { display: flex; align-items: center; gap: 12px; font-size: 1.8rem; font-weight: 700; color: var(--primary-pink); text-decoration: none; }
        .logo i { font-size: 2.2rem; background: var(--pink-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .logo span { background: var(--pink-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        nav ul { display: flex; list-style: none; gap: 2rem; }
        nav ul li a { text-decoration: none; color: var(--text-dark); font-weight: 500; font-size: 1rem; padding: 0.5rem 1rem; border-radius: 50px; transition: all 0.3s ease; }
        nav ul li a:hover { color: var(--primary-pink); background: rgba(255, 133, 162, 0.1); }
        nav ul li a.active { color: var(--white); background: var(--pink-gradient); box-shadow: 0 5px 15px rgba(255, 61, 127, 0.3); }

        .container { display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 100px 5% 60px; }
        .content-wrapper { display: flex; align-items: center; justify-content: space-between; max-width: 1300px; width: 100%; gap: 60px; }

        .hero-text { flex: 1; max-width: 600px; }
        .hero-text h1 { font-size: 3.5rem; line-height: 1.2; margin-bottom: 1.5rem; background: linear-gradient(90deg, var(--primary-pink), var(--pink-light)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero-text p { font-size: 1.2rem; color: var(--text-light); line-height: 1.8; margin-bottom: 2.5rem; }

        .stats { display: flex; gap: 2rem; margin-top: 2.5rem; }
        .stat-item { text-align: center; }
        .stat-number { font-size: 2.5rem; font-weight: 700; color: var(--primary-pink); line-height: 1; margin-bottom: 0.5rem; }
        .stat-label { font-size: 0.9rem; color: var(--text-light); font-weight: 500; }

        .login-card {
            flex: 1; max-width: 500px; background: var(--glass-bg); backdrop-filter: blur(20px);
            border-radius: var(--border-radius); padding: 3rem; box-shadow: var(--shadow);
            border: 1px solid rgba(255, 255, 255, 0.3); position: relative; overflow: hidden;
        }

        .login-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 5px; background: var(--pink-gradient); }
        .card-header { text-align: center; margin-bottom: 2.5rem; }
        .card-header h2 { font-size: 2rem; color: var(--text-dark); margin-bottom: 0.5rem; }
        .card-header p { color: var(--text-light); font-size: 1rem; }

        .login-options { display: flex; flex-direction: column; gap: 1.5rem; margin-bottom: 2rem; }
        .login-option { display: flex; align-items: center; justify-content: space-between; padding: 1.2rem 1.5rem; background: var(--white); border-radius: 15px; text-decoration: none; color: var(--text-dark); font-weight: 500; transition: all 0.3s ease; border: 2px solid transparent; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05); }
        .login-option:hover { transform: translateY(-5px); border-color: var(--primary-pink); box-shadow: 0 10px 25px rgba(255, 61, 127, 0.2); }
        
        .login-option.student { background: linear-gradient(135deg, rgba(255, 133, 162, 0.1) 0%, rgba(255, 61, 127, 0.05) 100%); }
        .login-option.admin { background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 235, 59, 0.05) 100%); }

        .option-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--white); }
        .student .option-icon { background: var(--pink-gradient); }
        .admin .option-icon { background: linear-gradient(135deg, #ff9800 0%, #ffc107 100%); }

        .option-text { flex: 1; margin-left: 1.5rem; }
        .option-text h3 { font-size: 1.2rem; margin-bottom: 0.2rem; }
        .option-text p { font-size: 0.9rem; color: var(--text-light); }
        .option-arrow { color: var(--text-light); font-size: 1.2rem; transition: transform 0.3s ease; }
        .login-option:hover .option-arrow { transform: translateX(5px); color: var(--primary-pink); }

        .divider { text-align: center; margin: 1.5rem 0; color: var(--text-light); position: relative; font-size: 0.9rem; }
        .divider span { background: var(--white); padding: 0 10px; position: relative; z-index: 1; }
        .divider::before { content: ""; position: absolute; top: 50%; left: 0; width: 100%; height: 1px; background: #e2e8f0; z-index: 0; }

        .google-login {
            display: flex; align-items: center; justify-content: center; gap: 12px; width: 100%; padding: 1rem;
            background: var(--white); border-radius: 15px; text-decoration: none; color: var(--text-dark);
            font-weight: 500; border: 2px solid #e2e8f0; transition: all 0.3s ease; margin-top: 1rem;
        }

        .google-login:hover { background: #f8fafc; border-color: var(--primary-pink); box-shadow: 0 5px 15px rgba(255, 61, 127, 0.1); }
        .google-icon { width: 22px; height: 22px; }

        footer { text-align: center; padding: 2rem; color: var(--text-light); font-size: 0.9rem; border-top: 1px solid rgba(255, 133, 162, 0.2); background: var(--glass-bg); backdrop-filter: blur(10px); }
        footer a { color: var(--primary-pink); text-decoration: none; font-weight: 600; }

        @media (max-width: 1024px) { .content-wrapper { flex-direction: column; text-align: center; } .hero-text, .login-card { max-width: 100%; } .stats { justify-content: center; } .hero-text h1 { font-size: 2.8rem; } }
    </style>
</head>
<body>

    <div class="floating-element"></div>
    <div class="floating-element"></div>
    <div class="floating-element"></div>

    <nav>
        <a href="#" class="logo">
            <i class="fas fa-graduation-cap"></i>
            <span>ScholarTrack</span>
        </a>
        <ul>
            <li><a href="#" class="active">Home</a></li>
            <li><a href="#">Courses</a></li>
            <li><a href="#">Dashboard</a></li>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="content-wrapper">
            <div class="hero-text">
                <h1>Empowering Education Through Intelligent Management</h1>
                <p>ScholarTrack revolutionizes student management with an intuitive platform that streamlines admissions, tracks academic progress, and enhances collaboration.</p>
                <div class="stats">
                    <div class="stat-item"><div class="stat-number">10,000+</div><div class="stat-label">Students Enrolled</div></div>
                    <div class="stat-item"><div class="stat-number">500+</div><div class="stat-label">Courses Available</div></div>
                    <div class="stat-item"><div class="stat-number">99%</div><div class="stat-label">Satisfaction Rate</div></div>
                </div>
            </div>

            <div class="login-card">
                <div class="card-header">
                    <h2>Welcome to ScholarTrack</h2>
                    <p>Select your login method to continue</p>
                </div>
                
                <div class="login-options">
                    <a href="views/student_login.php" class="login-option student">
                        <div class="option-icon"><i class="fas fa-user-graduate"></i></div>
                        <div class="option-text"><h3>Student Login</h3><p>Access your courses and grades</p></div>
                        <div class="option-arrow"><i class="fas fa-chevron-right"></i></div>
                    </a>
                    
                    <a href="views/admin_login.php" class="login-option admin">
                        <div class="option-icon"><i class="fas fa-user-shield"></i></div>
                        <div class="option-text"><h3>Administrator Login</h3><p>Manage system and student data</p></div>
                        <div class="option-arrow"><i class="fas fa-chevron-right"></i></div>
                    </a>
                </div>
                
                <div class="divider"><span>Or continue with</span></div>

                <?php
                    // PHP Logic for Google Login URL
                    $client_id = "682449346419-aqreg5epb9pkqp07q7imc1fqf4cfr8ot.apps.googleusercontent.com";
                    $redirect_uri = "http://localhost/students/google-callback.php";
                    $google_url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
                        'client_id' => $client_id,
                        'redirect_uri' => $redirect_uri,
                        'response_type' => 'code',
                        'scope' => 'email profile',
                        'access_type' => 'offline'
                    ]);
                ?>
                
                <a href="<?php echo $google_url; ?>" class="google-login">
                    <img src="https://cdn-icons-png.flaticon.com/512/2991/2991148.png" alt="Google" class="google-icon">
                    Continue with Google
                </a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2023 ScholarTrack. All rights reserved. | <a href="#">Privacy Policy</a></p>
    </footer>

    <script>
        // Keeping your original script for stats and scroll
        document.addEventListener('DOMContentLoaded', function() {
            const statNumbers = document.querySelectorAll('.stat-number');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = entry.target;
                        const finalValue = parseInt(target.textContent);
                        let currentValue = 0;
                        const timer = setInterval(() => {
                            currentValue += finalValue / 50;
                            if (currentValue >= finalValue) {
                                target.textContent = finalValue + (target.textContent.includes('+') ? '+' : '%');
                                clearInterval(timer);
                            } else { target.textContent = Math.floor(currentValue); }
                        }, 30);
                        observer.unobserve(target);
                    }
                });
            }, { threshold: 0.5 });
            statNumbers.forEach(stat => observer.observe(stat));
        });
    </script>
</body>
</html>