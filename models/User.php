<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($email, $password) {
        // Login ke liye 'user' table ka use
        $query = "SELECT * FROM user WHERE email = :email AND password = :pass LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':email' => $email,
            ':pass' => $password
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- Optimized Search Function with JOIN ---
    public function searchStudents($term) {
        // JOIN ka istemal kyunki name 'student' table mein hai aur role 'user' table mein
        $query = "SELECT s.id, s.name, u.email 
                  FROM student s 
                  JOIN user u ON s.user_id = u.id 
                  WHERE s.name LIKE :term 
                  AND u.role = 'student' 
                  ORDER BY s.name ASC 
                  LIMIT 10";
        
        $stmt = $this->conn->prepare($query);
        $searchTerm = $term . '%'; 
        $stmt->execute([':term' => $searchTerm]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>