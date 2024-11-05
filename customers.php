<?php
session_start();  // Start session

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");  // Redirect to login if not authenticated
    exit();
}

include 'db.php';
include 'navbar.php'; // Include navbar (acting as sidebar)

// Handle form submission for adding a new customer
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['customer_name'])) {
    $customer_name = $_POST['customer_name'];

    $sql = "INSERT INTO customers (customer_name) VALUES ('$customer_name')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('New customer added successfully.');</script>";
    } else {
        echo "<script>alert('Error: " . $sql . "<br>" . $conn->error . "');</script>";
    }
}

// Handle customer deletion
if (isset($_GET['delete'])) {
    $customer_id = $_GET['delete'];
    $delete_sql = "DELETE FROM customers WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $customer_id);
    if ($stmt->execute()) {
        echo "<script>alert('Customer deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting customer: " . $conn->error . "');</script>";
    }
}

// Display all customers
$sql = "SELECT * FROM customers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers</title>
    <link rel="stylesheet" href="Style/navbar.css"> <!-- Link to navbar CSS -->
    <link rel="stylesheet" href="Style/customers.css"> <!-- Link to page-specific CSS -->
</head>
<body>
    <div class="content"> <!-- Adjust margin for sidebar -->
        <h1>Manage Customers</h1>

        <!-- Form for adding new customer -->
        <form method="POST" action="customers.php">
            <label>Customer Name: </label>
            <input type="text" name="customer_name" required><br>
            <input type="submit" value="Add Customer">
        </form>

        <!-- Display all customers -->
        <h2>Existing Customers</h2>
        <?php
        if ($result->num_rows > 0) {
            echo '<table>';
            echo '<tr><th>Customer Name</th><th>Actions</th></tr>';
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . htmlspecialchars($row["customer_name"]) . "</td>";
                echo "<td><a href='customers.php?delete=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this customer?\");'>Delete</a></td></tr>";
            }
            echo '</table>';
        } else {
            echo "<p>No customers found.</p>";
        }
        ?>
    </div>
</body>
</html>
