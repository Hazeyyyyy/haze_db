<?php
session_start();
require_once 'database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_POST['customer_id'] ?? '';
    $order_date = $_POST['order_date'] ?? date('Y-m-d');
    $product_ids = $_POST['product_ids'] ?? [];
    $quantities = $_POST['quantities'] ?? [];

    if (empty($customer_id) || empty($product_ids)) {
        $error = 'Please select a customer and at least one product';
    } else {
        try {

            $stmt = $pdo->prepare("INSERT INTO orders (customer_id, order_date) VALUES (?, ?)");
            $stmt->execute([$customer_id, $order_date]);
            $order_id = $pdo->lastInsertId();

            foreach ($product_ids as $index => $product_id) {
                $quantity = $quantities[$index] ?? 1;
                
                $product_stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
                $product_stmt->execute([$product_id]);
                $product = $product_stmt->fetch();

                if ($product) {
                    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$order_id, $product_id, $quantity, $product['price']]);
                }
            }

            $_SESSION['message'] = 'Order created successfully!';
            header('Location: view_orders.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Error creating order: ' . $e->getMessage();
        }
    }
}

$customers = $pdo->query("SELECT id, name FROM customers ORDER BY name")->fetchAll();
$products = $pdo->query("SELECT id, name, price FROM products ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Order</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .product-item {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .remove-product {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .remove-product:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create New Order</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <a href="view_orders.php" class="btn-back">Back to Orders</a>

        <section class="form-section">
            <form method="POST" action="add_order.php" class="form">
                <div class="form-group">
                    <label for="customer_id">Select Customer:</label>
                    <select id="customer_id" name="customer_id" required>
                        <option value="">Choose a customer</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?php echo $customer['id']; ?>">
                                <?php echo htmlspecialchars($customer['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (count($customers) == 0): ?>
                        <p class="error-text">No customers found. <a href="add_customer.php">Add a customer first</a></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="order_date">Order Date:</label>
                    <input 
                        type="date" 
                        id="order_date" 
                        name="order_date" 
                        value="<?php echo date('Y-m-d'); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <h3>Select Products</h3>
                    <div id="products-container">
                        <div class="product-item">
                            <div style="display: flex; gap: 10px; align-items: flex-end;">
                                <div style="flex: 1;">
                                    <label>Product:</label>
                                    <select name="product_ids[]" required>
                                        <option value="">Choose a product</option>
                                        <?php foreach ($products as $product): ?>
                                            <option value="<?php echo $product['id']; ?>">
                                                <?php echo htmlspecialchars($product['name']); ?> (₱<?php echo number_format($product['price'], 2); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div style="flex: 0 0 100px;">
                                    <label>Quantity:</label>
                                    <input type="number" name="quantities[]" value="1" min="1" required>
                                </div>
                                <button type="button" class="remove-product" onclick="removeProduct(this)">Remove</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-secondary" onclick="addProduct()">Add Another Product</button>
                </div>

                <button type="submit" class="btn-primary">Create Order</button>
            </form>
        </section>
    </div>

    <script>
        function addProduct() {
            const container = document.getElementById('products-container');
            const newItem = document.createElement('div');
            newItem.className = 'product-item';
            newItem.innerHTML = `
                <div style="display: flex; gap: 10px; align-items: flex-end;">
                    <div style="flex: 1;">
                        <label>Product:</label>
                        <select name="product_ids[]" required>
                            <option value="">Choose a product</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['id']; ?>">
                                    <?php echo htmlspecialchars($product['name']); ?> (₱<?php echo number_format($product['price'], 2); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="flex: 0 0 100px;">
                        <label>Quantity:</label>
                        <input type="number" name="quantities[]" value="1" min="1" required>
                    </div>
                    <button type="button" class="remove-product" onclick="removeProduct(this)">Remove</button>
                </div>
            `;
            container.appendChild(newItem);
        }

        function removeProduct(button) {
            const container = document.getElementById('products-container');
            if (container.children.length > 1) {
                button.closest('.product-item').remove();
            } else {
                alert('You must have at least one product in the order');
            }
        }
    </script>
</body>
</html>