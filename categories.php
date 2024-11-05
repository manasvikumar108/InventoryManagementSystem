<?php
session_start();  // Start session

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");  // Redirect to login if not authenticated
    exit();
}

include 'db.php';
include 'navbar.php'; // Include navbar (acting as sidebar)

// Handle form submission for adding a new category
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = $_POST['category_name'];

    $sql = "INSERT INTO categories (name) VALUES ('$category_name')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Category added successfully.');</script>";
    } else {
        echo "<script>alert('Error: " . $sql . "<br>" . $conn->error . "');</script>";
    }
}

// Handle category deletion
if (isset($_GET['delete'])) {
    $category_id = $_GET['delete'];
    $delete_sql = "DELETE FROM categories WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $category_id);
    if ($stmt->execute()) {
        echo "<script>alert('Category deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting category: " . $conn->error . "');</script>";
    }
}

// Display all categories
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
    <link rel="stylesheet" href="Style/navbar.css"> <!-- Link to navbar CSS -->
    <link rel="stylesheet" href="Style/categories.css"> <!-- Link to page-specific CSS -->
</head>
<body>
    <div class="content"> <!-- Adjust margin for sidebar -->
        <h1>Category Management</h1>

        <!-- Form to add a new category -->
        <form method="POST" action="">
            <label>Category Name: </label>
            <input type="text" name="category_name" required><br>
            <input type="submit" value="Add Category">
        </form>

        <h2>Existing Categories</h2>
        <?php
        if ($result->num_rows > 0) {
            echo '<table>';
            echo '<tr><th>Category Name</th><th>Actions</th></tr>';
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . htmlspecialchars($row["name"]) . "</td>";
                echo "<td><a href='categories.php?delete=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this category?\");'>Delete</a></td></tr>";
            }
            echo '</table>';
        } else {
            echo "<p>No categories found.</p>";
        }
        ?>
    </div>
</body>
</html>
