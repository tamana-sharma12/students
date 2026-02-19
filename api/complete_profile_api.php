<?php
session_start();
header("Content-Type: application/json");
require_once "../config/Database.php";
$conn = Database::connect();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Login session expired!"]);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone   = $_POST['phone'];
    $class   = $_POST['class'];
    $gender  = $_POST['gender'];
    $address = $_POST['address'];

    // Database Update Query
    $stmt = $conn->prepare("UPDATE user SET phone=?, class=?, gender=?, address=? WHERE id=?");
    $success = $stmt->execute([$phone, $class, $gender, $address, $user_id]);

    if ($success) {
        echo json_encode(["status" => "success", "message" => "Details saved successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database update failed!"]);
    }
}
?>