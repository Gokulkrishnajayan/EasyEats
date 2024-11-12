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

// // Transfer data from orders to cart for the user (if the status is 'Pending')
// $query = "INSERT INTO cart (user_id, item_id, quantity, price, status, created_at, updated_at)
//           SELECT :user_id, item_id, quantity, price, 'Pending', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
//           FROM orders
//           WHERE user_id = :user_id AND status = 'Pending'";

// // Prepare and execute the query
// $stmt = $pdo->prepare($query);
// $stmt->execute([':user_id' => $user_id]);

// // Check if the data was successfully inserted
// if ($stmt->rowCount() > 0) {
//     echo "Items from your previous order have been successfully added to your cart.";
// } else {
//     echo "No pending orders found to add to the cart.";
// }

// Fetch the cart items for the user
$query = "SELECT c.order_id, c.quantity, c.price, c.delivery_date, c.status, i.name AS item_name, i.image AS item_image
          FROM cart c
          JOIN items i ON c.item_id = i.id
          WHERE c.user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute([':user_id' => $user_id]);

$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate the total price
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// Handle quantity update or item removal (if POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        // Update item quantity
        $order_id = $_POST['order_id'];
        $new_quantity = $_POST['quantity'];
        $query = "UPDATE cart SET quantity = :quantity WHERE order_id = :order_id AND user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':quantity' => $new_quantity, ':order_id' => $order_id, ':user_id' => $user_id]);
    } elseif (isset($_POST['remove'])) {
        // Remove item from cart
        $order_id = $_POST['order_id'];
        $query = "DELETE FROM cart WHERE order_id = :order_id AND user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':order_id' => $order_id, ':user_id' => $user_id]);
    }

    // Refresh the page to reflect changes
    header('Location: cart.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - EasyEats</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/cart.css"> <!-- Link to custom styles -->
    <link rel="stylesheet" href="css/navbar.css"> <!-- Link to custom styles -->
</head>
<body>
    <!-- Navbar (Home, Cart, etc.) -->
    <?php include('navbar.php'); ?>

    <div class="container my-5">
        <h1>Your Cart</h1>

        <?php if (empty($cart_items)): ?>
            <p>Your cart is empty. <a href="menu.php">Browse items</a> and add to your cart.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <img src="<?php echo $item['item_image']; ?>" alt="<?php echo $item['item_name']; ?>" width="100">
                                <?php echo $item['item_name']; ?>
                            </td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <form action="cart.php" method="POST" style="display:inline;">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" required>
                                    <input type="hidden" name="order_id" value="<?php echo $item['order_id']; ?>">
                                    <button type="submit" name="update" class="btn btn-primary btn-sm">Update</button>
                                </form>
                            </td>
                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            <td>
                                <form action="cart.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $item['order_id']; ?>">
                                    <button type="submit" name="remove" class="btn btn-danger btn-sm">Remove</button>
                                </form>
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

    <!-- Footer (Optional) -->
    <!-- <?php include('footer.php'); ?> -->

</body>
</html>
