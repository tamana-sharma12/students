<?php
header("Content-Type: application/json");
session_start();
require_once '../config/Database.php';
require_once '../controllers/AuthController.php';

$auth = new AuthController();
$result = $auth->login($_POST); 

if ($result === true) {
    echo json_encode(["status" => "success"]);
} elseif ($result === "verify_otp") {
    echo json_encode(["status" => "error", "message" => "Please verify OTP first", "redirect" => "verify_otp.php"]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid Email or Password! ❌"]);
}