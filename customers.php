<?php
session_start();  // Start session

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");  // Redirect to login if not authenticated
    exit();
}

include 'db.php';
include 'navbar.php';

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

echo '<div style="margin-left: 210px; padding: 20px;">';
echo '<h1>Manage Customers</h1>';

// Form for adding new customer
echo '<form method="POST" action="customers.php">
        <label>Customer Name: </label><input type="text" name="customer_name" required><br>
        <input type="submit" value="Add Customer">
      </form>';

// Display all customers
if ($result->num_rows > 0) {
    echo '<table border="1">';
    echo '<tr><th>Customer Name</th><th>Actions</th></tr>';
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["customer_name"] . "</td>";
        echo "<td><a href='customers.php?delete=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this customer?\");'>Delete</a></td></tr>";
    }
    echo '</table>';
} else {
    echo "No customers found.";
}

echo '</div>';
?>
