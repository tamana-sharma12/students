<?php
header("Content-Type: application/json");
require_once '../config/Database.php';
$db = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $class = $_POST['class'] ?? '';
    $phone = $_POST['phone'] ?? '';

    // Professional Validation
    if (empty($name) || empty($email) || empty($class) || empty($phone)) {
        echo json_encode(["status" => "error", "message" => "All fields are required!"]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format!"]);
        exit;
    }

    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        echo json_encode(["status" => "error", "message" => "Please enter a valid 10-digit phone number!"]);
        exit;
    }

    try {
        // 1. Pehle User table mein entry (Password default '123456' rakh sakte hain)
        $stmtUser = $db->prepare("INSERT INTO user (name, email, password, role) VALUES (?, ?, ?, 'student')");
        $stmtUser->execute([$name, $email, password_hash('123456', PASSWORD_DEFAULT)]);
        $user_id = $db->lastInsertId();

        // 2. Phir Student table mein entry
        $stmtStudent = $db->prepare("INSERT INTO student (user_id, class, phone) VALUES (?, ?, ?)");
        $stmtStudent->execute([$user_id, $class, $phone]);

        echo json_encode(["status" => "success", "message" => "Student registered successfully!"]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "System Error: " . $e->getMessage()]);
    }
}
?>