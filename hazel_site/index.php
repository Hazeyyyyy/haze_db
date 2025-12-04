<?php
include 'database.php'; // mysqli connection

// Fetch products with categories using LEFT JOIN
$sql = "SELECT p.productID, p.productName, p.price, c.categoryName
        FROM products p
        LEFT JOIN categories c ON p.categoryID = c.categoryID
        ORDER BY p.productID ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Products</h1>

    <!-- Navigation Card -->
    <div class="nav-card">
        <a href="add_product.php">Add Product</a>
        <a href="add_categories.php">Add Categories</a>
        <a href="add_customer.php">Add Customer</a>
        <a href="add_order.php">Add Order</a>
    </div>

    <!-- Products Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
        <?php if($result && $result->num_rows > 0): ?>
            <?php while($product = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $product['productID'] ?></td>
                    <td><?= htmlspecialchars($product['productName']) ?></td>
                    <td><?= $product['categoryName'] ?? 'Uncategorized' ?></td>
                    <td>$<?= number_format($product['price'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align:center;">No products found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
