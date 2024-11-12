<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to the admin login page if not logged in or not an admin
    header('Location: admin-login.php');  
    exit;
}

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "easyeats";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert new category
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = $_POST['category_name'];

    $sql = "INSERT INTO categories (category_name) VALUES ('$category_name')";
    if ($conn->query($sql) === TRUE) {
        echo "Category added!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navbar.css"> <!-- Link to navbar styles -->
    <title>Category Management - EasyEats</title>
</head>
<body>

    <!-- Include Navbar -->
    <?php include('navbar.php'); ?>

    <h1>Category Management</h1>
    <form action="category-management.php" method="POST">
        <input type="text" name="category_name" placeholder="Enter category name" required>
        <br>
        <button type="submit">Add Category</button>
    </form>
</body>
</html>
