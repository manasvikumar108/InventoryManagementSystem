<?php
include 'db.php';  // Include the database connection

// Initialize a variable to hold messages
$message = "";  // This will be empty if there's no message

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username already exists
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Set error message for existing username
        $message = "<div class='message error'>Username already exists. Please choose another one.</div>";
    } else {
        // Insert the new user (in a real application, password should be hashed)
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        if ($conn->query($sql) === TRUE) {
            // Set success message for signup
            $message = "<div class='message success'>Signup successful! You can now <a href='login.php'>Login</a>.</div>";
        } else {
            // Set error message for any other issues
            $message = "<div class='message error'>Error: " . $conn->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="./Style/signup.css">
</head>
<body>
    <div class="container">
        <h2>Signup</h2>

        <!-- Display the message if it exists -->
        <div class="message-container">
            <?php echo $message; ?>
        </div>

        <form method="POST" action="signup.php">
            <label>Username:</label>
            <input type="text" name="username" required><br>
            <label>Password:</label>
            <input type="password" name="password" required><br>
            <input type="submit" value="Signup">
        </form>
    </div>
</body>
</html>
