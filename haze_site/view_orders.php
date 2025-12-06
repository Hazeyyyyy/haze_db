<?php
session_start();
require_once 'database.php';

$orders = $pdo->query("
  SELECT o.id, o.order_date, c.name AS customer_name, COUNT(oi.id) AS total_items
  FROM orders o
  JOIN customers c ON o.customer_id = c.id
  LEFT JOIN order_items oi ON o.id = oi.order_id
  GROUP BY o.id, o.order_date, c.name, o.customer_id
  ORDER BY o.order_date DESC
")->fetchAll();
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
        <h1>All Orders</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <nav class="navbar">
            <ul>
                <li><a href="index.php">Products</a></li>
                <li><a href="add_category.php">Add Category</a></li>
                <li><a href="add_product.php">Add Product</a></li>
                <li><a href="add_customer.php">Add Customer</a></li>
                <li><a href="add_order.php" class="btn-primary-nav">Create Order</a></li>
                <li><a href="view_orders.php" class="active">View Orders</a></li>
            </ul>
        </nav>

        <section class="orders-section">
            <?php if (count($orders) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer Name</th>
                            <th>Order Date</th>
                            <th>Total Items</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['id']); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($order['order_date']))); ?></td>
                                <td><?php echo htmlspecialchars($order['total_items']); ?></td>
                                <td>
                                    <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn-view">View Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No orders found. <a href="add_order.php">Create your first order</a></p>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
