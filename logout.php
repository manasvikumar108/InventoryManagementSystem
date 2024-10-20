<?php
session_start();  // Start the session
session_destroy();  // Destroy all session data

// Redirect to the login page
header("Location: login.php");
exit();
?>
