<?php
session_start();
header("Content-Type: application/json");
require_once "../config/Database.php";
$conn = Database::connect();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Login Required"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

// ACTION 1: SEND OTP
if ($action == 'send_otp') {
    $phone = $_POST['phone'] ?? '';
    $otp = rand(100000, 999999); 

    $stmt = $conn->prepare("UPDATE user SET temp_phone = ?, phone_otp = ? WHERE id = ?");
    if ($stmt->execute([$phone, $otp, $user_id])) {
        echo json_encode(["status" => "success", "message" => "OTP Sent!", "mock_otp" => $otp]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database Error"]);
    }
}

// ACTION 2: VERIFY OTP
if ($action == 'verify_otp') {
    $otp = $_POST['otp'] ?? '';
    $stmt = $conn->prepare("SELECT temp_phone, phone_otp FROM user WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user && $user['phone_otp'] == $otp) {
        $update = $conn->prepare("UPDATE user SET phone = ?, temp_phone = NULL, phone_otp = NULL WHERE id = ?");
        $update->execute([$user['temp_phone'], $user_id]);
        echo json_encode(["status" => "success", "message" => "Phone Updated!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid OTP"]);
    }
}
?>