<?php
session_start(); // Start the session

// Remove specific session variables
if (isset($_SESSION['user_id'])) {
    unset($_SESSION['user_id']);
}

// Destroy the entire session
session_unset();
session_destroy();

// Redirect to admin-landingpage.php
header('Location: ./src/pages/admin-landingpage.php'); // Adjust the path if needed
exit();
?>
