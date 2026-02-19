<?php
header("Content-Type: application/json");
require_once '../config/Database.php'; // Controller ki jagah direct DB use karein zyada accurate rahega
$db = Database::connect();
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    echo json_encode(["status" => "error", "message" => "Unauthorized!"]);
    exit;
}

$id = $_POST['id'] ?? 0;

if($id > 0) {
    try {
        $db->beginTransaction();

        // Pehle student table se user_id nikaliye
        $stmt = $db->prepare("SELECT user_id FROM student WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $user_id = $row['user_id'];

            // 1. Delete from student table first
            $db->prepare("DELETE FROM student WHERE id = ?")->execute([$id]);

            // 2. Delete from user table next
            $db->prepare("DELETE FROM user WHERE id = ?")->execute([$user_id]);

            $db->commit();
            echo json_encode(["status" => "success", "message" => "Student and User deleted!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Student not found!"]);
        }
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid ID!"]);
}
?>