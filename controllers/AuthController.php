<?php
require_once '../config/Database.php';

class AuthController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    public function login($data) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $email = htmlspecialchars(trim($data['email']));
        $password = trim($data['password']);

        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE email = :email AND role = 'student'");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Password check
        if ($user && $password === $user['password']) {
            
            // OTP Check logic
            if ($user['is_verified'] == 0) {
                $_SESSION['verify_email'] = $user['email'];
                return "verify_otp"; // Sirf message return karein
            }

            // Session set karein
            $_SESSION['user_id'] = $user['id']; 
            $_SESSION['student_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            return true; // Success return karein
        }

        return false; // Fail return karein
    }
}