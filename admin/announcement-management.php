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

// Insert announcement
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $announcement = $_POST['announcement'];

    $sql = "INSERT INTO announcements (announcement_text) VALUES ('$announcement')";
    if ($conn->query($sql) === TRUE) {
        echo "New announcement posted!";
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
    <title>Announcement Management - EasyEats</title>
</head>
<body>
    <h1>Announcement Management</h1>
    <form action="announcement-management.php" method="POST">
        <textarea name="announcement" rows="4" cols="50" placeholder="Enter announcement here..."></textarea>
        <br>
        <button type="submit">Post Announcement</button>
    </form>
</body>
</html>
