<?php
session_start();

// Admin Credentials
// You can change these to whatever you prefer
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>