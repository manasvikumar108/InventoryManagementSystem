<?php
session_start();
include 'db.php';
include 'navbar.php';

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Fetch available items
$sql = "SELECT * FROM items";
$items_result = $conn->query($sql);

// Fetch available customers
$sql = "SELECT * FROM customers";
$customers_result = $conn->query($sql);

// Handle purchase form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_id'], $_POST['quantity'], $_POST['customer_id'])) {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    $customer_id = $_POST['customer_id'];

    // Fetch the item details
    $sql = "SELECT * FROM items WHERE id = '$item_id'";
    $item_result = $conn->query($sql);
    $item = $item_result->fetch_assoc();

    if ($item['stock'] >= $quantity) {
        // Update stock
        $new_stock = $item['stock'] - $quantity;
        $sql = "UPDATE items SET stock = '$new_stock' WHERE id = '$item_id'";
        $conn->query($sql);

        // Calculate total price
        $total_price = $item['price'] * $quantity;

        // Create billing entry
        $sql = "INSERT INTO billing (item_id, quantity, total_price, customer_id) VALUES ('$item_id', '$quantity', '$total_price', '$customer_id')";
        $conn->query($sql);

        // Fetch the customer name
        $sql = "SELECT customer_name FROM customers WHERE id = '$customer_id'";
        $customer_result = $conn->query($sql);
        $customer = $customer_result->fetch_assoc();
        $customer_name = $customer['customer_name'];

        echo "<h3>Purchase successful!</h3>";
        echo "<p>Total Price: $" . $total_price . "</p>";
        echo "<p>Customer: " . $customer_name . "</p>";
        echo "<p>Item: " . $item['name'] . "</p>";
        echo "<p>Quantity: " . $quantity . "</p>";
        echo "<p><strong>Thank you for your purchase!</strong></p>";
    } else {
        echo "<script>alert('Not enough stock available.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing</title>
</head>
<body>
    <h1>Billing System</h1>

    <!-- Form to handle billing -->
    <form method="POST" action="billing.php">
        <label for="customer_id">Select Customer:</label>
        <select name="customer_id" required>
            <option value="">Select a customer</option>
            <?php
            if ($customers_result->num_rows > 0) {
                while ($row = $customers_result->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['customer_name'] . "</option>";
                }
            } else {
                echo "<option value=''>No customers available</option>";
            }
            ?>
        </select><br>

        <label for="item_id">Select Item:</label>
        <select name="item_id" required>
            <option value="">Select an item</option>
            <?php
            if ($items_result->num_rows > 0) {
                while ($row = $items_result->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['name'] . " - $" . $row['price'] . "</option>";
                }
            } else {
                echo "<option value=''>No items available</option>";
            }
            ?>
        </select><br>

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" required><br>

        <input type="submit" value="Generate Billing">
    </form>
</body>
</html>
