<?php
// API folder se Config aur Models tak pahunchne ke liye '../' lagaya hai
require_once '../config/Database.php'; 
require_once '../models/User.php';

header('Content-Type: application/json');

try {
    $db = Database::connect(); 

    if (!$db) {
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }

    $user = new User($db);

    $term = isset($_GET['term']) ? $_GET['term'] : '';

    if (!empty($term)) {
        // Dhyaan dein: User model mein 'searchStudents' function hona zaroori hai
        $results = $user->searchStudents($term);
        echo json_encode($results);
    } else {
        echo json_encode([]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>