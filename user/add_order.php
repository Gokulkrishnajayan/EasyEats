<?php
session_start();
include('../db.php'); // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // Redirect to login page if not logged in or not a user
    header('Location: login.php');
    exit;
}

// Check if the form was submitted with necessary fields
if (isset($_POST['item_id'], $_POST['item_name'], $_POST['order_type'])) {
    $item_id = $_POST['item_id'];
    $item_name = $_POST['item_name'];
    $order_type = $_POST['order_type'];
    $customization = isset($_POST['customization']) ? $_POST['customization'] : NULL;
    $username = $_SESSION['username']; // Assuming the user's name is stored in the session
    
    // Determine the order status based on order type
    $status = ($order_type === 'dayorder') ? 'Approved' : 'Pending';
    
    // Prepare the SQL query to insert the order into the database
    $query = "INSERT INTO orders (username, item_name, order_datetime, customization, status, order_type) 
              VALUES (:username, :item_name, CURRENT_TIMESTAMP, :customization, :status, :order_type)";
    
    // Prepare the statement
    $stmt = $pdo->prepare($query);
    
    // Bind the parameters
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':item_name', $item_name);
    $stmt->bindParam(':customization', $customization);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':order_type', $order_type);
    
    // Execute the query
    if ($stmt->execute()) {
        // Redirect to a confirmation page or display a success message
        header('Location: cart.php'); // Example page for confirmation
        exit;
    } else {
        echo "Error: Could not place the order.";
    }
} else {
    // If form is not properly submitted, redirect or show error
    echo "Error: Missing required information.";
}
?>
