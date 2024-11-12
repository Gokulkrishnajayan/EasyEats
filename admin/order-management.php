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

// Fetch orders
$sql = "SELECT * FROM orders";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - EasyEats</title>
</head>
<body>
    <h1>Order Management</h1>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User Name</th>
                <th>Item</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['order_id']; ?></td>
                <td><?php echo $row['user_name']; ?></td>
                <td><?php echo $row['item_name']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td><a href="update-order.php?id=<?php echo $row['order_id']; ?>">Update</a></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
