<?php
header("Content-Type: application/json");
require_once "../config/database.php";
$conn = Database::connect();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';

    if (empty($email) || empty($pass)) {
        echo json_encode(["status" => "error", "message" => "Kuch fields khali hain!"]);
        exit;
    }

    // Password Update Query
    $stmt = $conn->prepare("UPDATE user SET password=? WHERE email=?");
    $result = $stmt->execute([$pass, $email]);

    if ($result) {
        echo json_encode(["status" => "success", "message" => "Password successfully badal gaya hai!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database update fail ho gaya."]);
    }
}
?>