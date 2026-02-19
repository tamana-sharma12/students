<?php
header("Content-Type: application/json");
require_once "../config/database.php";
$conn = Database::connect();

$email = $_POST['email'] ?? '';

if (!empty($email)) {
    $stmt = $conn->prepare("SELECT id FROM user WHERE email=?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        // Link generate karein (par ye link ab JSON mein jayega)
        $reset_link = "reset_password.php?email=" . urlencode($email);
        echo json_encode([
            "status" => "success",
            "message" => "Email found! ✔",
            "link" => $reset_link
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Email not found ❌"
        ]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Email is required"]);
}
?>