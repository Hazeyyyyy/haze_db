<?php
include 'database.php';

// Fetch all orders with customer name
$orders = $conn->query("
    SELECT o.id, o.order_date, o.total_amount, c.name AS customer_name
    FROM orders o
    JOIN customers c ON o.customer_id = c.id
    ORDER BY o.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Orders</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div align="center">
        <h1>Orders</h1>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Products</th>
                <th>Total Amount</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if($orders->num_rows > 0): ?>
            <?php while($order = $orders->fetch_assoc()): ?>
                <?php
                // Fetch products for this order
                $stmt = $conn->prepare("
                    SELECT p.name, oi.quantity
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?
                ");
                $stmt->bind_param("i", $order['id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $products = [];
                while($row = $result->fetch_assoc()) {
                    $products[] = $row['name']." (x".$row['quantity'].")";
                }
                $stmt->close();
                ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td><?= implode(", ", $products) ?></td>
                    <td>₱<?= number_format($order['total_amount'],2) ?></td>
                    <td><?= $order['order_date'] ?></td>
                    <td>
                        <a href="order_details.php?id=<?= $order['id'] ?>" class="btn">View</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No orders found</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="button-container">
        <a href="add_order.php" class="button">Add Order</a>
        <a href="index.php" class="button">Back to Dashboard</a>
    </div>
</div>
</body>
</html>