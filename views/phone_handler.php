<?php
session_start();
require_once "../config/Database.php";
$conn = Database::connect();

// Security: Check karein ki user login hai
if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized Access";
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

// --- 1. ACTION: SEND OTP ---
if ($action == 'send_otp') {
    $new_phone = $_POST['phone'];
    $otp = rand(100000, 999999); // 6-digit random OTP generate kiya

    // Database mein temporary save karein taaki verify kiya ja sake
    $stmt = $conn->prepare("UPDATE user SET temp_phone = ?, phone_otp = ? WHERE id = ?");
    $result = $stmt->execute([$new_phone, $otp, $user_id]);

    if ($result) {
        // Yahan real life mein SMS API call hoti hai
        // Filhaal hum sirf testing ke liye "OTP Sent" bhej rahe hain
        echo "OTP Sent. (Mock OTP: $otp)"; 
    } else {
        echo "Error: Could not save OTP";
    }
}

// --- 2. ACTION: VERIFY OTP ---
if ($action == 'verify_otp') {
    $entered_otp = $_POST['otp'];

    // Database se saved OTP aur temp number uthayein
    $stmt = $conn->prepare("SELECT temp_phone, phone_otp FROM user WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $entered_otp == $user['phone_otp']) {
        // SUCCESS: OTP match ho gaya! Ab main phone number update karein
        $new_number = $user['temp_phone'];
        
        $update = $conn->prepare("UPDATE user SET phone = ?, temp_phone = NULL, phone_otp = NULL WHERE id = ?");
        $update->execute([$new_number, $user_id]);

        echo "Success";
    } else {
        // FAIL: OTP galat hai
        echo "Invalid OTP";
    }
}
?>