<?php
require_once '../config/Database.php';

class StudentController {
    public $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    private function sanitize($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    public function add($data) {
        $clean_name  = $this->sanitize($data['name']);
        $clean_email = $this->sanitize($data['email']);
        $clean_class = $this->sanitize($data['class']);
        $clean_phone = $this->sanitize($data['phone']);

        try {
            // 1. Check if email already exists in USER table
            $check = $this->db->prepare("SELECT id FROM user WHERE email = ?");
            $check->execute([$clean_email]);
            if ($check->fetch()) {
                echo "<script>alert('Ye Email User table mein pehle se hai!'); window.location='admin_dashboard.php';</script>";
                exit;
            }

            // 2. Pehle USER table mein entry (Kyunki Foreign Key zaroori hai)
            $stmt1 = $this->db->prepare("INSERT INTO user (name, email, password, role) VALUES (?, ?, ?, 'student')");
            $stmt1->execute([$clean_name, $clean_email, 'student123']);
            
            // New User ki ID nikaalein
            $new_user_id = $this->db->lastInsertId();

            // 3. Ab STUDENT table mein entry (Sahi user_id ke saath)
            $query = "INSERT INTO student (user_id, name, class, email, password, phone, role) 
                      VALUES (?, ?, ?, ?, ?, ?, 'student')";
            
            $stmt2 = $this->db->prepare($query);
            $stmt2->execute([
                $new_user_id, 
                $clean_name, 
                $clean_class, 
                $clean_email, 
                'student123', 
                $clean_phone
            ]);

            return true;
        } catch (Exception $e) {
            die("Database Error: " . $e->getMessage());
        }
    }

    public function getAll(){
        // JOIN query taaki latest data dikhe
        $stmt = $this->db->query("
            SELECT s.id, s.user_id, u.name, s.class, u.email, s.phone 
            FROM student s 
            JOIN user u ON s.user_id = u.id
            ORDER BY s.id DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id){
        $id = (int)$id; 
        $stmt = $this->db->prepare("SELECT user_id FROM student WHERE id=?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($user){
            $user_id = $user['user_id'];
            // Pehle Student delete karo (Child), phir User (Parent)
            $this->db->prepare("DELETE FROM student WHERE id=?")->execute([$id]);
            $this->db->prepare("DELETE FROM user WHERE id=?")->execute([$user_id]);
        }
    }
}
?>