<?php
include 'database.php';

// Fetch existing customers and products
$customers = $conn->query("SELECT * FROM customers ORDER BY name ASC");
$products = $conn->query("SELECT * FROM products ORDER BY name ASC");

// Handle form submission
if (isset($_POST['submit'])) {
    $customer_id = $_POST['customer_id'];
    $product_ids = $_POST['product_id'];
    $quantities = $_POST['quantity'];

    $total_amount = 0;

    // Calculate total amount
    foreach ($product_ids as $index => $pid) {
        $stmt = $conn->prepare("SELECT price FROM products WHERE id=?");
        $stmt->bind_param("i", $pid);
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $stmt->close();

        $total_amount += $price * $quantities[$index];
    }

    // Insert into orders table
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, total_amount) VALUES (?, ?)");
    $stmt->bind_param("id", $customer_id, $total_amount);

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;
        $stmt->close();

        // Insert each product into order_items
        foreach ($product_ids as $index => $pid) {
            $stmt = $conn->prepare("SELECT price FROM products WHERE id=?");
            $stmt->bind_param("i", $pid);
            $stmt->execute();
            $stmt->bind_result($price);
            $stmt->fetch();
            $stmt->close();

            $quantity = $quantities[$index];
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $order_id, $pid, $quantity, $price);
            $stmt->execute();
            $stmt->close();
        }

        $message = "Order added successfully!";
        $message_class = "success";
    } else {
        $message = "Error: " . $stmt->error;
        $message_class = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Order</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div align="center">
        <h1>Add Order</h1>
    </div>

    <?php if(isset($message)): ?>
        <p class="<?= $message_class ?>"><?= $message ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label>Select Customer:</label>
        <select name="customer_id" required>
            <option value="">Select Customer</option>
            <?php while($cust = $customers->fetch_assoc()): ?>
                <option value="<?= $cust['id'] ?>"><?= htmlspecialchars($cust['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <h3>Products</h3>
        <div id="products-container">
            <div class="product-item">
                <select name="product_id[]" required>
                    <option value="">Select Product</option>
                    <?php while($prod = $products->fetch_assoc()): ?>
                        <option value="<?= $prod['id'] ?>"><?= htmlspecialchars($prod['name']) ?> - ₱<?= $prod['price'] ?></option>
                    <?php endwhile; ?>
                </select>
                <input type="number" name="quantity[]" min="1" value="1" required>
            </div>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <button type="button" class="btn btn-primary" onclick="addProduct()">Add Another Product</button>
        </div>

        <br>
        <div style="text-align: center; margin-top: 20px;">
            <button type="submit" name="submit" class="btn btn-primary">Add Order</button>
        </div>
    </form>

    <div class="button-container">
        <a href="index.php" class="button">Back to Dashboard</a>
    </div>
</div>

<script>
function addProduct() {
    const container = document.getElementById('products-container');
    const div = document.createElement('div');
    div.classList.add('product-item');
    div.innerHTML = `
        <select name="product_id[]" required>
            <option value="">Select Product</option>
            <?php
            $products2 = $conn->query("SELECT * FROM products ORDER BY name ASC");
            while($prod2 = $products2->fetch_assoc()): ?>
                <option value="<?= $prod2['id'] ?>"><?= htmlspecialchars($prod2['name']) ?> - ₱<?= $prod2['price'] ?></option>
            <?php endwhile; ?>
        </select>
        <input type="number" name="quantity[]" min="1" value="1" required>
    `;
    container.appendChild(div);
}
</script>
</body>
</html>