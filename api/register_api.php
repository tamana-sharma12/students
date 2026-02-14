<?php
ob_start(); 
header("Content-Type: application/json"); 
session_start();
// Aapka folder structure: api se bahar nikal kar config mein jana hai
require_once '../config/Database.php';

$response = ["status" => "error", "message" => "Kuch gadbad ho gayi"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = Database::connect();
        
        $name = htmlspecialchars(trim($_POST['name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $password = $_POST['password']; 
        $otp = rand(100000, 999999);

        // Check if email exists
        $stmt = $db->prepare("SELECT id FROM user WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            ob_clean();
            echo json_encode(["status" => "error", "message" => "Email pehle se hai!"]);
            exit;
        }

        $sql = "INSERT INTO user (name, email, password, role, verification_code, is_verified) 
                VALUES (?, ?, ?, 'student', ?, 0)";
        $stmt = $db->prepare($sql);
        
        if ($stmt->execute([$name, $email, $password, $otp])) {
            $_SESSION['verify_email'] = $email;
            $response = ["status" => "success", "message" => "Data Saved!", "otp" => $otp];
        }
    } catch (PDOException $e) {
        $response = ["status" => "error", "message" => "Database Error: " . $e->getMessage()];
    }
}
ob_clean(); 
echo json_encode($response);
exit;