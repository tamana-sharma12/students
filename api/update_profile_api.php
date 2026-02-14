<?php
header("Content-Type: application/json");
session_start();
require_once '../config/Database.php';

$response = ["status" => "error", "message" => "Access denied"];

// Nayi ID ke liye session check ko mazboot banayein
if (isset($_SESSION['user_id']) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $db = Database::connect();
    
    $phone = htmlspecialchars(trim($_POST['phone']));
    $class = htmlspecialchars(trim($_POST['class']));
    $address = htmlspecialchars(trim($_POST['address']));
    $user_id = $_SESSION['user_id']; // Login ke waqt set hota hai
    $new_otp = rand(100000, 999999);

    try {
        // Query check karein ki columns match ho rahe hain
        $sql = "UPDATE user SET phone = ?, class = ?, address = ?, phone_otp = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        
        if ($stmt->execute([$phone, $class, $address, $new_otp, $user_id])) {
            $response = ["status" => "success", "message" => "Profile Updated!", "otp" => $new_otp];
        } else {
            $response = ["status" => "error", "message" => "Update Failed in DB"];
        }
    } catch (PDOException $e) {
        $response = ["status" => "error", "message" => "Database Error: " . $e->getMessage()];
    }
}
echo json_encode($response);