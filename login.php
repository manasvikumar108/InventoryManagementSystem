<?php
include 'db.php';  // Include the database connection

// Check if any user exists in the database
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    // If no user exists, redirect to the signup page
    header("Location: signup.php");
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check credentials in the database
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Successful login, start session
        session_start();
        $_SESSION['user'] = $username;  // Store username in session
        header("Location: index.php");  // Redirect to the dashboard
    } else {
        echo "Invalid login credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form method="POST" action="login.php">
        <label>Username:</label>
        <input type="text" name="username" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>
    <p>Don't have an account? <a href="signup.php">Signup here</a>.</p>  <!-- Link to signup page -->
</body>
</html>
