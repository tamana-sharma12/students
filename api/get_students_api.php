<?php
header("Content-Type: application/json");
require_once '../config/Database.php';
$db = Database::connect();

try {
    // Sirf students ki list fetch karein
    $stmt = $db->query("SELECT s.id, s.class, s.phone, s.user_id, u.name, u.email 
                        FROM student s 
                        JOIN user u ON s.user_id = u.id 
                        ORDER BY s.id DESC");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($students);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>