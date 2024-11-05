<?php
include 'db.php';  // Include the database connection

// Redirect to signup if no user exists
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: signup.php");
    exit();
}

// Initialize message variable
$message = "";

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
        exit();
    } else {
        // Set the message for invalid credentials
        $message = "Invalid login credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./Style/login.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>

        <!-- Message container to hold PHP output messages -->
        <div class="message-container">
            <?php
                if (!empty($message)) {
                    echo "<div class='message error'>$message</div>";
                }
            ?>
        </div>

        <form method="POST" action="login.php">
            <label>Username:</label>
            <input type="text" name="username" required><br>
            <label>Password:</label>
            <input type="password" name="password" required><br>
            <input type="submit" value="Login">
        </form>
        <p>Don't have an account? <a href="signup.php">Signup here</a>.</p>
    </div>
</body>
</html>
