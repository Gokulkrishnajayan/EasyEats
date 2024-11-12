<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
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
    <title>Category Management - EasyEats</title>
</head>
<body>
    <h1>Category Management</h1>
    <form action="category-management.php" method="POST">
        <input type="text" name="category_name" placeholder="Enter category name" required>
        <br>
        <button type="submit">Add Category</button>
    </form>
</body>
</html>
