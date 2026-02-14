<?php
session_start();
require_once "config/Database.php"; 
$conn = Database::connect();

$client_id = '682449346419-aqreg5epb9pkqp07q7imc1fqf4cfr8ot.apps.googleusercontent.com';
//$client_secret = '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'; 
//$redirect_uri = 'http://localhost/students/google-callback.php'; 

$client_id = "HIDDEN_FOR_SECURITY";
$client_secret = "HIDDEN_FOR_SECURITY";

if (isset($_GET['code'])) {
    // 1. Google se Token lena
    $url = 'https://oauth2.googleapis.com/token';
    $params = [
        'code' => $_GET['code'],
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
        'grant_type' => 'authorization_code'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (isset($data['access_token'])) {
        $access_token = $data['access_token'];
        $user_info = json_decode(file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $access_token), true);

        $email = $user_info['email'];
        $google_id = $user_info['id']; 
        $name = $user_info['name'];

        // 2. Database mein User check karein
        $stmt = $conn->prepare("SELECT * FROM user WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // --- FIX 1: DASHBOARD KE MUTABIQ SESSION KEYS ---
            $_SESSION['user_id'] = $user['id']; 
            $_SESSION['role'] = $user['role']; // Yeh 'student' hona chahiye DB mein
            $_SESSION['student_name'] = $user['name'];

            // Agar DB mein role 'student' nahi hai toh dashboard nahi khulega
            if ($user['role'] !== 'student') {
                die("Access Denied: You are not a student.");
            }

            // Logic: Profile completion check
            if (empty($user['phone']) || empty($user['class'])) {
                header("Location: views/complete_profile.php");
            } else {
                header("Location: views/student_dashboard.php");
            }
            exit();

        } else {
            // NAYA USER HAI
            $insert_stmt = $conn->prepare("INSERT INTO user (name, email, google_id, role, is_verified) VALUES (:name, :email, :google_id, 'student', 1)");
            $insert_stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':google_id' => $google_id
            ]);

            $new_id = $conn->lastInsertId();

            // --- FIX 2: NAYE USER KE LIYE BHI SAHI SESSIONS ---
            $_SESSION['user_id'] = $new_id;
            $_SESSION['role'] = 'student';
            $_SESSION['student_name'] = $name;

            header("Location: views/complete_profile.php");
            exit();
        }
    } else {
        die("Google Token Error!");
    }
}
?>