<?php
session_start();
require_once 'database.php';

$products = $pdo->query("
  SELECT p.id, p.name, p.price, c.name AS category_name
  FROM products p
  JOIN categories c ON p.category_id = c.id
  ORDER BY p.name
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProStock System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>ProStockSystem</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <nav class="navbar">
            <ul>
                <li><a href="index.php" class="active">Products</a></li>
                <li><a href="add_category.php">Add Category</a></li>
                <li><a href="add_product.php">Add Product</a></li>
                <li><a href="add_customer.php">Add Customer</a></li>
                <li><a href="add_order.php">Create Order</a></li>
                <li><a href="view_orders.php">View Orders</a></li>
            </ul>
        </nav>

        <section class="products-section">
            <h2>All Products</h2>
            <?php if (count($products) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                <td>₱<?php echo number_format($product['price'], 2); ?></td>
                                <td>
                                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn-edit">Edit</a>
                                    <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No products found. <a href="add_product.php">Add your first product</a></p>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
