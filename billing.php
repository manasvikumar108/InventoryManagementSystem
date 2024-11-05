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

// Initialize variables
$item_stock = 0; // Default stock value

// Handle purchase form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_id'], $_POST['quantity'], $_POST['customer_id'])) {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    $customer_id = $_POST['customer_id'];

    // Fetch the item details
    $sql = "SELECT * FROM items WHERE id = '$item_id'";
    $item_result = $conn->query($sql);

    // Check if the item exists
    if ($item_result->num_rows > 0) {
        $item = $item_result->fetch_assoc();
        $item_stock = $item['stock']; // Store stock for later use

        if ($item_stock >= $quantity) {
            // Update stock
            $new_stock = $item_stock - $quantity;
            $sql = "UPDATE items SET stock = '$new_stock' WHERE id = '$item_id'";
            if ($conn->query($sql) === TRUE) {
                // Calculate total price
                $total_price = $item['price'] * $quantity;

                // Create billing entry
                $sql = "INSERT INTO billing (item_id, quantity, total_price, customer_id) VALUES ('$item_id', '$quantity', '$total_price', '$customer_id')";
                if ($conn->query($sql) === TRUE) {
                    // Fetch the customer name
                    $sql = "SELECT customer_name FROM customers WHERE id = '$customer_id'";
                    $customer_result = $conn->query($sql);
                    $customer = $customer_result->fetch_assoc();
                    $customer_name = $customer['customer_name'];

                    echo "<div class='confirmation'>
                            <h3>Purchase successful!</h3>
                            <p>Total Price: <strong>$" . number_format($total_price, 2) . "</strong></p>
                            <p>Customer: <strong>" . htmlspecialchars($customer_name) . "</strong></p>
                            <p>Item: <strong>" . htmlspecialchars($item['name']) . "</strong></p>
                            <p>Quantity: <strong>" . htmlspecialchars($quantity) . "</strong></p>
                            <p><strong>Thank you for your purchase!</strong></p>
                          </div>";
                } else {
                    echo "<script>alert('Error creating billing entry: " . $conn->error . "');</script>";
                }
            } else {
                echo "<script>alert('Error updating stock: " . $conn->error . "');</script>";
            }
        } else {
            echo "<script>alert('Not enough stock available. Only $item_stock in stock.');</script>";
        }
    } else {
        echo "<script>alert('Item not found.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing System</title>
    <link rel="stylesheet" href="Style/navbar.css"> <!-- Link to navbar CSS -->
    <link rel="stylesheet" href="Style/billing.css"> <!-- Link to page-specific CSS -->
    <script>
        // Function to update the max quantity based on the selected item
        function updateMaxQuantity(selectElement) {
            var selectedOption = selectElement.options[selectElement.selectedIndex];
            var stock = selectedOption.getAttribute('data-stock');
            document.getElementById('quantity').max = stock; // Set the max quantity input
        }
    </script>
</head>
<body>
    <div class="content"> <!-- Adjust margin for sidebar -->
        <h1>Billing System</h1>

        <!-- Form to handle billing -->
        <form method="POST" action="billing.php">
            <label for="customer_id">Select Customer:</label>
            <select name="customer_id" required>
                <option value="">Select a customer</option>
                <?php
                if ($customers_result->num_rows > 0) {
                    while ($row = $customers_result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['customer_name']) . "</option>";
                    }
                } else {
                    echo "<option value=''>No customers available</option>";
                }
                ?>
            </select><br>

            <label for="item_id">Select Item:</label>
            <select name="item_id" id="item_id" required onchange="updateMaxQuantity(this)">
                <option value="">Select an item</option>
                <?php
                if ($items_result->num_rows > 0) {
                    while ($row = $items_result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "' data-stock='" . $row['stock'] . "'>" . htmlspecialchars($row['name']) . " - $" . number_format($row['price'], 2) . "</option>";
                    }
                } else {
                    echo "<option value=''>No items available</option>";
                }
                ?>
            </select><br>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" required min="1" max="<?php echo $item_stock; ?>"><br>

            <input type="submit" value="Generate Billing">
        </form>
    </div>
</body>
</html>
