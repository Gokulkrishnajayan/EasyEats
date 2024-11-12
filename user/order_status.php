<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

// Include database connection
include('../db.php');

// Get the user_id from the session
$user_id = $_SESSION['user_id'];

// Updated query to fetch orders for the user
$query = "SELECT o.order_id, o.quantity, o.price, o.delivery_date, o.status, o.order_type, o.created_at, i.name AS item_name, i.image AS item_image
          FROM cart o
          JOIN items i ON o.item_id = i.id
          WHERE o.user_id = :user_id";

$stmt = $pdo->prepare($query);
$stmt->execute([':user_id' => $user_id]);

$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate the total price (optional, if applicable)
$total_price = 0;
foreach ($order_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// Handle actions (Cancel or Pay Now)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel'])) {
        // Cancel the order
        $order_id = $_POST['order_id'];
        $query = "UPDATE orders SET status = 'Cancelled' WHERE order_id = :order_id AND user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':order_id' => $order_id, ':user_id' => $user_id]);
    } elseif (isset($_POST['paynow'])) {
        // Update the status to Paid
        $order_id = $_POST['order_id'];
        $query = "UPDATE orders SET status = 'Paid' WHERE order_id = :order_id AND user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':order_id' => $order_id, ':user_id' => $user_id]);
    }

    // Refresh the page to reflect changes
    header('Location: order_status.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status - EasyEats</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/order_status.css"> <!-- Link to custom styles -->
    <link rel="stylesheet" href="css/navbar.css"> <!-- Link to custom styles -->
</head>
<body>
    <!-- Navbar (Home, Cart, etc.) -->
    <?php include('navbar.php'); ?>

    <div class="container my-5">
        <h1>Your Order Status</h1>

        <?php if (empty($order_items)): ?>
            <p>You don't have any orders. <a href="menu.php">Browse items</a> and place an order.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Order Type</th>
                        <th>Order Date</th>
                        <th>Delivery Date</th>
                        <th>Status</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td>
                                <?php echo $item['item_name']; ?>
                                <img src="../uploads/<?php echo $item['item_image']; ?>" alt="<?php echo $item['item_name']; ?>" width="100">
                            </td>
                            <td>
                                <span class="form-control-plaintext"><?php echo ucfirst($item['order_type']); ?></span>
                            </td>
                            <td>
                                <input type="text" class="form-control" value="<?php echo $item['created_at']; ?>" disabled>
                            </td>
                            <td>
                                <input type="text" class="form-control" value="<?php echo $item['delivery_date']; ?>" disabled>
                            </td>
                            <td>
                                <span class="form-control-plaintext"><?php echo ucfirst($item['status']); ?></span>
                            </td>
                            <td>
                                $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </td>
                            <td>
                                <!-- Action buttons (Cancel or Pay Now) -->
                                <?php if ($item['status'] === 'Pending'): ?>
                                    <!-- Cancel button -->
                                    <form action="order_status.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="order_id" value="<?php echo $item['order_id']; ?>">
                                        <button type="submit" name="cancel" class="btn btn-danger btn-sm">Cancel</button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if ($item['status'] === 'Pending'): ?>
                                    <!-- Pay Now button -->
                                    <form action="order_status.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="order_id" value="<?php echo $item['order_id']; ?>">
                                        <button type="submit" name="paynow" class="btn btn-success btn-sm">Pay Now</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="text-right">
                <h3>Total: $<?php echo number_format($total_price, 2); ?></h3>
                <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
