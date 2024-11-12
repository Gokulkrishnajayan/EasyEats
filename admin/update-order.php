<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to the admin login page if not logged in or not an admin
    header('Location: admin-login.php');
    exit;
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    // Get the order ID and action
    $order_id = $_GET['id'];
    $action = $_GET['action'];

    // Validate the action (either "approve" or "reject")
    if ($action === 'approve' || $action === 'reject') {
        // Set the new status based on the action
        $status = ($action === 'approve') ? 'Approved' : 'Rejected';

        // Connect to the database
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "easyeats";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Update the status of the order in the database
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $order_id); // Bind the parameters to the prepared statement
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Redirect back to the order management page after updating the status
            header('Location: order-management.php');
            exit;
        } else {
            echo "Error updating order status.";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Invalid action.";
    }
} else {
    echo "Invalid request.";
}
?>
