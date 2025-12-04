<?php
include 'database.php'; // mysqli connection

$error = $success = "";

// Fetch customers for dropdown
$customersResult = $conn->query("SELECT customerID, customerName FROM customers ORDER BY customerName ASC");

// Fetch products for selection
$productsResult = $conn->query("SELECT productID, productName, price FROM products ORDER BY productName ASC");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customerID = $_POST['customerID'] ?? null;
    $productIDs = $_POST['productID'] ?? [];
    $quantities = $_POST['quantity'] ?? [];

    if (empty($customerID) || empty($productIDs)) {
        $error = "Please select a customer and at least one product.";
    } else {
        // Start transaction
        $conn->begin_transaction();
        try {
            // Insert into orders table
            $stmtOrder = $conn->prepare("INSERT INTO orders (customerID) VALUES (?)");
            $stmtOrder->bind_param("i", $customerID);
            $stmtOrder->execute();
            $orderID = $stmtOrder->insert_id;
            $stmtOrder->close();

            // Insert into order_items table
            $stmtItem = $conn->prepare("INSERT INTO order_items (orderID, productID, quantity, price) VALUES (?, ?, ?, ?)");
            for ($i = 0; $i < count($productIDs); $i++) {
                $prodID = (int)$productIDs[$i];
                $qty = (int)$quantities[$i];

                // Get product price
                $priceResult = $conn->query("SELECT price FROM products WHERE productID = $prodID");
                $row = $priceResult->fetch_assoc();
                $price = $row['price'];

                $stmtItem->bind_param("iiid", $orderID, $prodID, $qty, $price);
                $stmtItem->execute();
            }
            $stmtItem->close();

            $conn->commit();
            $success = "Order added successfully!";
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Order</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Add New Order</h1>

    <!-- Navigation Card -->
    <div class="nav-card">
        <a href="add_product.php">Add Product</a>
        <a href="add_categories.php">Add Categories</a>
        <a href="add_customer.php">Add Customer</a>
        <a href="add_order.php">Add Orders</a>
    </div>

    <!-- Form Container -->
    <div class="form-container">
        <form method="POST" action="">
            <?php if($error): ?>
                <p style="color:red; text-align:center; margin-bottom:15px;"><?= $error ?></p>
            <?php endif; ?>
            <?php if($success): ?>
                <p style="color:green; text-align:center; margin-bottom:15px;"><?= $success ?></p>
            <?php endif; ?>

            <label for="customerID">Select Customer:</label>
            <select name="customerID" id="customerID" required>
                <option value="">-- Select Customer --</option>
                <?php while($customer = $customersResult->fetch_assoc()): ?>
                    <option value="<?= $customer['customerID'] ?>"><?= htmlspecialchars($customer['customerName']) ?></option>
                <?php endwhile; ?>
            </select>

            <h3>Products</h3>
            <?php while($product = $productsResult->fetch_assoc()): ?>
                <div style="margin-bottom:10px;">
                    <input type="checkbox" name="productID[]" value="<?= $product['productID'] ?>" id="prod<?= $product['productID'] ?>">
                    <label for="prod<?= $product['productID'] ?>"><?= htmlspecialchars($product['productName']) ?> ($<?= number_format($product['price'],2) ?>)</label>
                    <input type="number" name="quantity[]" min="1" value="1" style="width:60px; margin-left:10px;">
                </div>
            <?php endwhile; ?>

            <input type="submit" value="Add Order">
        </form>
    </div>
</body>
</html>
