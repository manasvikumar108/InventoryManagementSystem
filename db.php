<?php
// Database connection settings
$host = 'localhost';
$user = 'root';       // Default MySQL user
$pass = '';           // MySQL root password (usually blank in XAMPP)
$dbname = 'shop_management';  // Your database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
