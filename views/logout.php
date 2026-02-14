<?php
session_start();
// Saare session variables ko clear karna
session_unset();
// Session ko khatam karna
session_destroy();

// Index page par redirect karna (Path check karein agar index bahar hai toh ../ use karein)
header("Location: ../index.php"); 
exit();
?>