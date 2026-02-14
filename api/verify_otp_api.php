<?php
ob_start();
header("Content-Type: application/json");
session_start();
require_once '../config/Database.php';

$response = ["status" => "error", "message" => "Invalid Request"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = Database::connect();
        
        $email = $_SESSION['verify_email'] ?? '';
        $user_otp = htmlspecialchars(trim($_POST['otp']));

        if (empty($email)) {
            echo json_encode(["status" => "error", "message" => "Session expired. Please register again."]);
            exit;
        }

        // Step 1: OTP check karein
        $stmt = $db->prepare("SELECT id FROM user WHERE email = ? AND verification_code = ?");
        $stmt->execute([$email, $user_otp]);

        if ($stmt->fetch()) {
            // Step 2: Account verify karein
            $update = $db->prepare("UPDATE user SET is_verified = 1, verification_code = NULL WHERE email = ?");
            if ($update->execute([$email])) {
                unset($_SESSION['verify_email']); 
                $response = ["status" => "success", "message" => "Verified Successfully!"];
            } else {
                $response = ["status" => "error", "message" => "Database update failed."];
            }
        } else {
            $response = ["status" => "error", "message" => "Galti! Sahi OTP dalein jo database mein hai."];
        }
    } catch (PDOException $e) {
        $response = ["status" => "error", "message" => "Error: " . $e->getMessage()];
    }
}

ob_clean();
echo json_encode($response);
exit;