<?php
include 'database.php';
$products = $conn->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard - Products</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1 align="center">Dashboard</h1>

    <div class="dashboard-cards">
    <a href="manage_categories.php" class="card">Categories</a>
    <a href="add_product.php" class="card">Add Product</a>
    <a href="add_customer.php" class="card">Add Customer</a>
    <a href="view_orders.php" class="card">View Orders</a>
    <a href="add_order.php" class="card">Add Order</a> 
</div>


    <h2 align="center">Products</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
        <?php if($products->num_rows > 0): ?>
            <?php while($row = $products->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                    <td>₱<?= number_format($row['price'], 2) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td>
                        <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn">Edit</a>
                        <a href="delete_product.php?id=<?= $row['id'] ?>" class="btn">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No products found</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
