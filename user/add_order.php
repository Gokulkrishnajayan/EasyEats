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
    $user_id = $_SESSION['user_id']; // Assuming the user ID is stored in the session
    
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
        // After the order is placed, insert data into the cart table
        $order_id = $pdo->lastInsertId(); // Get the ID of the last inserted order
        
        // Get item price (assuming you have a price column in the 'items' table)
        $price_query = "SELECT price FROM items WHERE id = :item_id";
        $price_stmt = $pdo->prepare($price_query);
        $price_stmt->execute([':item_id' => $item_id]);
        $item = $price_stmt->fetch(PDO::FETCH_ASSOC);
        $price = $item['price'];
        
        // Insert the item into the cart table, including the order_type
        $cart_query = "INSERT INTO cart (user_id, item_id, quantity, price, status, order_type, created_at, updated_at)
                       VALUES (:user_id, :item_id, 1, :price, :status, :order_type, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        
        // Prepare the statement for the cart insertion
        $cart_stmt = $pdo->prepare($cart_query);
        $cart_stmt->execute([
            ':user_id' => $user_id,
            ':item_id' => $item_id,
            ':status' => $status,
            ':price' => $price,
            ':order_type' => $order_type // Add the order_type to the cart insertion
        ]);
        
        // Redirect to the cart page after successful insertion
        header('Location: cart.php');
        exit;
    } else {
        echo "Error: Could not place the order.";
    }
} else {
    // If form is not properly submitted, redirect or show error
    echo "Error: Missing required information.";
}
?>
