<?php
session_start();
require_once 'database.php';

$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    header('Location: view_orders.php');
    exit;
}

$order_stmt = $pdo->prepare("
  SELECT o.id, o.order_date, c.id AS customer_id, c.name AS customer_name, c.email
  FROM orders o
  JOIN customers c ON o.customer_id = c.id
  WHERE o.id = ?
");
$order_stmt->execute([$order_id]);
$order = $order_stmt->fetch();

if (!$order) {
    $_SESSION['error'] = 'Order not found';
    header('Location: view_orders.php');
    exit;
}

$items_stmt = $pdo->prepare("
  SELECT oi.id, oi.quantity, oi.price, p.id AS product_id, p.name AS product_name
  FROM order_items oi
  JOIN products p ON oi.product_id = p.id
  WHERE oi.order_id = ?
  ORDER BY p.name
");
$items_stmt->execute([$order_id]);
$orderDetails = $items_stmt->fetchAll();

$total = 0;
foreach ($orderDetails as $item) {
    $total += $item['quantity'] * $item['price'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Order Details</h1>

        <a href="view_orders.php" class="btn-back">Back to Orders</a>

        <section class="order-info">
            <div class="info-card">
                <h2>Order #<?php echo htmlspecialchars($order['id']); ?></h2>
                <div class="info-row">
                    <strong>Order Date:</strong>
                    <span><?php echo htmlspecialchars(date('Y-m-d', strtotime($order['order_date']))); ?></span>
                </div>
                <div class="info-row">
                    <strong>Customer Name:</strong>
                    <span><?php echo htmlspecialchars($order['customer_name']); ?></span>
                </div>
                <div class="info-row">
                    <strong>Customer Email:</strong>
                    <span><?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?></span>
                </div>
            </div>
        </section>

        <section class="order-items">
            <h2>Order Items</h2>
            <?php if (count($orderDetails) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price per Unit</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderDetails as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                <td>₱<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td colspan="3"><strong>Total:</strong></td>
                            <td><strong>₱<?php echo number_format($total, 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No items in this order.</p>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
