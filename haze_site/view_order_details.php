<?php
include 'database.php';

if (!isset($_GET['id'])) {
    die("Order ID is missing.");
}

$order_id = $_GET['id'];

$stmt = $conn->prepare("
    SELECT o.id, o.order_date, o.total_amount, c.name AS customer_name
    FROM orders o
    JOIN customers c ON o.customer_id = c.id
    WHERE o.id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    die("Order not found.");
}

$stmt = $conn->prepare("
    SELECT oi.quantity, oi.price, p.name AS product_name
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order_items = [];
while ($row = $result->fetch_assoc()) {
    $order_items[] = $row;
}
$stmt->close();
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
    <h1>Order Details - Order #<?= $order['id'] ?></h1>
    <a href="view_orders.php">Back to Orders</a>

    <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
    <p><strong>Order Date:</strong> <?= $order['order_date'] ?></p>
    <p><strong>Total Amount:</strong> ₱<?= number_format($order['total_amount'], 2) ?></p>

    <h2>Products</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Subtotal</th>
        </tr>
        <?php foreach ($order_items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>₱<?= number_format($item['price'], 2) ?></td>
            <td>₱<?= number_format($item['quantity'] * $item['price'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>