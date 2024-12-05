<?php
session_start();
require_once 'includes/functions.php';

// Verify CSRF token
verify_csrf_token($_GET['csrf_token'] ?? '');

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: login.php");
exit();