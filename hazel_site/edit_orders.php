<?php
include 'database.php'; // mysqli connection

// Get order ID from URL
if(!isset($_GET['id']) || empty($_GET['id'])){
    die("Order ID is required.");
}
$orderID = (int)$_GET['id'];

// Initialize messages
$error = $success = "";

// Handle form submission to update quantities
if($_SERVER["REQUEST_METHOD"] == "POST"){
    foreach($_POST['quantity'] as $orderItemID => $qty){
        $qty = (int)$qty;
        if($qty <= 0){
            // Delete item if quantity is 0 or less
            $stmt = $conn->prepare("DELETE FROM order_items WHERE orderitemID = ?");
            $stmt->bind_param("i", $orderItemID);
            $stmt->execute();
            $stmt->close();
        } else {
            // Update quantity
            $stmt = $conn->prepare("UPDATE order_items SET quantity = ? WHERE orderitemID = ?");
            $stmt->bind_param("ii", $qty, $orderItemID);
            $stmt->execute();
            $stmt->close();
        }
    }
    $success = "Order updated successfully!";
}

// Fetch order items with product names
$sql = "
    SELECT oi.orderitemID, p.productName, oi.quantity, oi.price
    FROM order_items oi
    JOIN products p ON oi.productID = p.productID
    WHERE oi.orderID = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderID);
$stmt->execute();
$result = $stmt->get_result();

// Fetch order details
$orderItems = [];
$orderTotal = 0;
while($row = $result->fetch_assoc()){
    $row['total'] = $row['price'] * $row['quantity'];
    $orderTotal += $row['total'];
    $orderItems[] = $row;
}

// Fetch customer info
$orderStmt = $conn->prepare("SELECT c.customerName, o.orderDate FROM orders o JOIN customers c ON o.customerID = c.customerID WHERE o.orderID = ?");
$orderStmt->bind_param("i", $orderID);
$orderStmt->execute();
$orderResult = $orderStmt->get_result()->fetch_assoc();

$stmt->close();
$orderStmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Order #<?= $orderID ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Edit Order #<?= $orderID ?></h1>

    <!-- Navigation Card -->
    <div class="nav-card">
        <a href="add_product.php">Add Product</a>
        <a href="add_categories.php">Add Categories</a>
        <a href="add_customer.php">Add Customer</a>
        <a href="add_order.php">Add Orders</a>
    </div>

    <div class="form-container">
        <?php if($error) echo "<p style='color:red; text-align:center;'>$error</p>"; ?>
        <?php if($success) echo "<p style='color:green; text-align:center;'>$success</p>"; ?>

        <form method="POST" action="">
            <p><strong>Customer:</strong> <?= htmlspecialchars($orderResult['customerName']) ?></p>
            <p><strong>Order Date:</strong> <?= $orderResult['orderDate'] ?></p>

            <table>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
                <?php foreach($orderItems as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['productName']) ?></td>
                    <td>
                        <input type="number" name="quantity[<?= $item['orderitemID'] ?>]" value="<?= $item['quantity'] ?>" min="0" style="width:60px;">
                    </td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td>$<?= number_format($item['total'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="3" style="text-align:right;">Order Total:</th>
                    <th>$<?= number_format($orderTotal, 2) ?></th>
                </tr>
            </table>

            <input type="submit" value="Update Order">
        </form>
    </div>
</body>
</html>
