<?php
session_start();  // Start session

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");  // Redirect to login if not authenticated
    exit();  // Stop further execution
}

include 'db.php';  // Include the database connection
include 'navbar.php';  // Include the navbar

// Fetch the latest purchases (bills)
$sql = "SELECT billing.id, customers.customer_name, items.name AS item_name, billing.quantity, billing.total_price 
        FROM billing 
        JOIN customers ON billing.customer_id = customers.id 
        JOIN items ON billing.item_id = items.id 
        ORDER BY billing.id DESC 
        LIMIT 5";  // Fetch the latest 5 purchases

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="Style/index.css"> <!-- Link to additional page-specific CSS -->
</head>
<body>
    <div class="content"> <!-- Adjust margin to accommodate navbar -->
        <h1>Welcome to the Dashboard, <?php echo $_SESSION['user']; ?>!</h1>
        <h2>Latest Purchases</h2>

        <?php
        if ($result->num_rows > 0) {
            echo '<table>';
            echo '<thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                    </tr>
                  </thead>';
            echo '<tbody>';
            
            // Loop through and display each purchase
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['id'] . "</td>
                        <td>" . $row['customer_name'] . "</td>
                        <td>" . $row['item_name'] . "</td>
                        <td>" . $row['quantity'] . "</td>
                        <td>$" . $row['total_price'] . "</td>
                      </tr>";
            }
            
            echo '</tbody>';
            echo '</table>';
        } else {
            echo "<p>No recent purchases found.</p>";
        }
        ?>
    </div>
</body>
</html>
