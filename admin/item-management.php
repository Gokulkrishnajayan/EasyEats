<?php
session_start();

// Include database connection
include('db_connection.php');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to the admin login page if not logged in or not an admin
    header('Location: admin-login.php');  
    exit;
}

// Fetch categories for the dropdown
$categories_result = mysqli_query($conn, "SELECT * FROM categories");

// Add item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_item'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category'];
    $image = $_FILES['image']['name'];
    $image_temp = $_FILES['image']['tmp_name'];

    // Move the image to the upload directory
    move_uploaded_file($image_temp, 'uploads/' . $image);

    // Insert new item into the database
    $query = "INSERT INTO items (name, description, price, category_id, image) 
              VALUES ('$name', '$description', '$price', '$category_id', '$image')";
    mysqli_query($conn, $query);
    header("Location: item_management.php");
}

// Delete item
if (isset($_GET['delete'])) {
    $item_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM items WHERE id = $item_id");
    header("Location: item_management.php");
}

// Fetch items
$items_result = mysqli_query($conn, "SELECT * FROM items JOIN categories ON items.category_id = categories.id");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navbar.css"> <!-- Link to navbar styles -->
    <title>Item Management</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>

    <!-- Include Navbar -->
    <?php include('navbar.php'); ?>

    <div class="container">
        <h2>Item Management</h2>

        <!-- Add Item Form -->
        <form action="item_management.php" method="POST" enctype="multipart/form-data">
            <h3>Add New Item</h3>
            <input type="text" name="name" placeholder="Item Name" required><br>
            <textarea name="description" placeholder="Item Description" required></textarea><br>
            <input type="number" name="price" placeholder="Price" required><br>
            <select name="category" required>
                <option value="">Select Category</option>
                <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                    <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                <?php endwhile; ?>
            </select><br>
            <input type="file" name="image" required><br>
            <button type="submit" name="add_item">Add Item</button>
        </form>

        <h3>Manage Existing Items</h3>
        <table>
            <tr>
                <th>Item Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Price</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>

            <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
            <tr>
                <td><?= $item['name'] ?></td>
                <td><?= $item['category_name'] ?></td>
                <td><?= $item['description'] ?></td>
                <td>$<?= number_format($item['price'], 2) ?></td>
                <td><img src="uploads/<?= $item['image'] ?>" alt="Item Image" width="100px"></td>
                <td>
                    <a href="item_management.php?delete=<?= $item['id'] ?>" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>

        </table>
    </div>

</body>
</html>

