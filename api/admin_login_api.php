<?php
header("Content-Type: application/json");
session_start();
include_once '../config/Database.php';

$response = ["status" => "error", "message" => "Unknown error"];

// Sanitization function
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = Database::connect();
    
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password']; 
    $user_captcha = sanitize_input($_POST['captcha_input']);

    // 1. Captcha Validation
    if ($user_captcha != $_SESSION['captcha_ans']) {
        $response = ["status" => "error", "message" => "Wrong Captcha! ❌"];
        // Naya captcha generate karna
        $n1 = rand(1, 9); $n2 = rand(1, 9);
        $_SESSION['captcha_ans'] = $n1 + $n2;
        $_SESSION['captcha_text'] = "$n1 + $n2";
        $response['new_captcha'] = $_SESSION['captcha_text'];
    } 
    else {
        // 2. Admin verification
        $stmt = $db->prepare("SELECT * FROM admin WHERE email = :email AND password = :pass LIMIT 1");
        $stmt->execute([':email' => $email, ':pass' => $password]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            $_SESSION['role'] = 'admin'; 
            $_SESSION['admin_name'] = $admin['name'];
            $response = ["status" => "success", "message" => "Login successful"];
        } else {
            $response = ["status" => "error", "message" => "Invalid Admin Credentials! ❌"];
        }
    }
}
echo json_encode($response);