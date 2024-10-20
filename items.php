<?php
session_start();
include 'db.php';       // Include database connection
include 'navbar.php';   // Include navbar (assuming you have a navbar)

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Fetch all categories for the category dropdown
$sql = "SELECT * FROM categories";
$categories_result = $conn->query($sql);

// Handle item form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_name'], $_POST['category_id'], $_POST['stock'], $_POST['manufacturer'], $_POST['price'])) {
    $item_name = $_POST['item_name'];
    $category_id = $_POST['category_id'];
    $stock = $_POST['stock'];
    $manufacturer = $_POST['manufacturer'];
    $price = $_POST['price'];

    // Validate input
    if (!empty($item_name) && !empty($category_id) && !empty($stock) && !empty($manufacturer) && !empty($price)) {
        // Insert new item into the database
        $sql = "INSERT INTO items (name, category_id, stock, manufacturer, price) VALUES ('$item_name', '$category_id', '$stock', '$manufacturer', '$price')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Item added successfully!');</script>";
        } else {
            echo "<script>alert('Error adding item: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('All fields are required');</script>";
    }
}

// Handle item deletion
if (isset($_GET['delete'])) {
    $item_id = $_GET['delete'];
    $delete_sql = "DELETE FROM items WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $item_id);
    if ($stmt->execute()) {
        echo "<script>alert('Item deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting item: " . $conn->error . "');</script>";
    }
}

// Fetch all items to display
$sql = "SELECT items.id, items.name, categories.name AS category_name, items.stock, items.manufacturer, items.price FROM items JOIN categories ON items.category_id = categories.id";
$items_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items Management</title>
</head>
<body>

<h1>Items Management</h1>

<!-- Form to add a new item -->
<form method="POST" action="items.php">
    <label for="item_name">Item Name:</label>
    <input type="text" name="item_name" required><br>

    <label for="category_id">Category:</label>
    <select name="category_id" required>
        <option value="">Select a category</option>
        <?php
        if ($categories_result->num_rows > 0) {
            while ($row = $categories_result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
            }
        } else {
            echo "<option value=''>No categories available</option>";
        }
        ?>
    </select><br>

    <label for="stock">Stock:</label>
    <input type="number" name="stock" required><br>

    <label for="manufacturer">Manufacturer:</label>
    <input type="text" name="manufacturer" required><br>

    <label for="price">Price Per Item:</label>
    <input type="number" name="price" step="0.01" required><br>

    <input type="submit" value="Add Item">
</form>

<h2>Existing Items</h2>
<table border="1">
    <thead>
        <tr>
            <th>Item Name</th>
            <th>Category</th>
            <th>Stock</th>
            <th>Manufacturer</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Check if there are any items to display
        if ($items_result->num_rows > 0) {
            // Loop through and display items
            while ($row = $items_result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['name']}</td>
                        <td>{$row['category_name']}</td>
                        <td>{$row['stock']}</td>
                        <td>{$row['manufacturer']}</td>
                        <td>{$row['price']}</td>
                        <td>
                            <a href='items.php?delete={$row['id']}' onclick='return confirm(\"Are you sure you want to delete this item?\");'>Delete</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No items found</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>
