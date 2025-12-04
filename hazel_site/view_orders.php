<?php
include 'database.php'; // mysqli connection

// Fetch orders with customer and order items
$sql = "
    SELECT o.orderID, o.orderDate, c.customerName, 
           oi.productID, p.productName, oi.quantity, oi.price
    FROM orders o
    JOIN customers c ON o.customerID = c.customerID
    JOIN order_items oi ON o.orderID = oi.orderID
    JOIN products p ON oi.productID = p.productID
    ORDER BY o.orderID ASC, oi.orderitemID ASC
";

$result = $conn->query($sql);

// Organize data by order
$orders = [];
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $orderID = $row['orderID'];
        if(!isset($orders[$orderID])){
            $orders[$orderID] = [
                'customerName' => $row['customerName'],
                'orderDate' => $row['orderDate'],
                'items' => []
            ];
        }
        $orders[$orderID]['items'][] = [
            'productName' => $row['productName'],
            'quantity' => $row['quantity'],
            'price' => $row['price']
        ];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Orders List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Orders</h1>

    <!-- Navigation Card -->
    <div class="nav-card">
        <a href="add_product.php">Add Product</a>
        <a href="add_categories.php">Add Categories</a>
        <a href="add_customer.php">Add Customer</a>
        <a href="add_order.php">Add Orders</a>
    </div>

    <?php if(empty($orders)): ?>
        <p style="text-align:center;">No orders found.</p>
    <?php else: ?>
        <?php foreach($orders as $orderID => $order): ?>
            <table>
                <tr>
                    <th colspan="4">Order #<?= $orderID ?> | Customer: <?= htmlspecialchars($order['customerName']) ?> | Date: <?= $order['orderDate'] ?></th>
                </tr>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
                <?php 
                $orderTotal = 0;
                foreach($order['items'] as $item): 
                    $total = $item['price'] * $item['quantity'];
                    $orderTotal += $total;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($item['productName']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>$<?= number_format($item['price'], 2) ?></td>
                        <td>$<?= number_format($total, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="3" style="text-align:right;">Order Total:</th>
                    <th>$<?= number_format($orderTotal, 2) ?></th>
                </tr>
            </table>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
