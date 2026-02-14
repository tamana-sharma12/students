<?php
require_once '../controllers/StudentController.php';
$studentCtrl = new StudentController();
session_start();

// Check if admin
if($_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

// Get student id from URL
$id = $_GET['id'] ?? 0;

// Delete student
$studentCtrl->delete($id);

// Redirect back to admin dashboard
header("Location: admin_dashboard.php");
exit;
