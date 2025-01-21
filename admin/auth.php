<?php
session_start();

// Redirect to login page if the admin is not logged in
if (!isset($_SESSION['login_admin_id'])) {
    header('Location:login.php');
    exit;
}
?>