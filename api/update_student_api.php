<?php
header("Content-Type: application/json");
require_once '../config/Database.php'; // Apne database file ka sahi path check kar lein
$db = Database::connect();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Security Check: Sirf admin hi update kar sakta hai
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access!"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Data get karein
    $id      = $_POST['id'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    $name    = $_POST['name'] ?? '';
    $email   = $_POST['email'] ?? '';
    $class   = $_POST['class'] ?? '';
    $phone   = $_POST['phone'] ?? '';

    if (empty($id) || empty($user_id)) {
        echo json_encode(["status" => "error", "message" => "Invalid ID or User ID!"]);
        exit;
    }

    try {
        $db->beginTransaction();

        // 1. User table update karein (Name aur Email)
        $stmt1 = $db->prepare("UPDATE user SET name=?, email=? WHERE id=?");
        $stmt1->execute([$name, $email, $user_id]);

        // 2. Student table update karein (Class aur Phone)
        $stmt2 = $db->prepare("UPDATE student SET class=?, phone=? WHERE id=?");
        $stmt2->execute([$class, $phone, $id]);

        $db->commit();
        echo json_encode(["status" => "success", "message" => "Student updated successfully! ✔"]);

    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(["status" => "error", "message" => "Database Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method!"]);
}
?>