<?php
include 'database.php'; // mysqli connection

// Get product ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Product ID is missing.");
}

$productID = (int)$_GET['id'];

// Initialize variables
$productName = $price = $categoryID = "";
$error = $success = "";

// Fetch categories for dropdown
$categoriesResult = $conn->query("SELECT categoryID, categoryName FROM categories ORDER BY categoryName ASC");

// Fetch product details
$stmt = $conn->prepare("SELECT productName, price, categoryID FROM products WHERE productID = ?");
$stmt->bind_param("i", $productID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();
$productName = $product['productName'];
$price = $product['price'];
$categoryID = $product['categoryID'];

$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productName = trim($_POST['productName']);
    $price = trim($_POST['price']);
    $categoryID = $_POST['categoryID'] ?: null;

    if (empty($productName) || empty($price)) {
        $error = "Product name and price are required.";
    } else {
        $stmt = $conn->prepare("UPDATE products SET productName = ?, price = ?, categoryID = ? WHERE productID = ?");
        if ($categoryID === null) {
            $stmt->bind_param("sdii", $productName, $price, $categoryID, $productID);
        } else {
            $categoryID = (int)$categoryID;
            $stmt->bind_param("sdii", $productName, $price, $categoryID, $productID);
        }

        if ($stmt->execute()) {
            $success = "Product updated successfully!";
        } else {
            $error = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Edit Product</h1>

    <!-- Navigation Card -->
    <div class="nav-card">
        <a href="add_product.php">Add Product</a>
        <a href="manage_categories.php">Add Categories</a>
        <a href="add_customer.php">Add Customer</a>
        <a href="add_order.php">Add Orders</a>
    </div>

    <!-- Form Container -->
    <div class="form-container">
        <form method="POST" action="">
            <?php if($error) echo "<p style='color:red; text-align:center;'>$error</p>"; ?>
            <?php if($success) echo "<p style='color:green; text-align:center;'>$success</p>"; ?>

            <label for="productName">Product Name:</label>
            <input type="text" name="productName" id="productName" value="<?= htmlspecialchars($productName) ?>" required>

            <label for="price">Price:</label>
            <input type="number" step="0.01" name="price" id="price" value="<?= htmlspecialchars($price) ?>" required>

            <label for="categoryID">Category:</label>
            <select name="categoryID" id="categoryID">
                <option value="">-- Select Category --</option>
                <?php 
                $categoriesResult = $conn->query("SELECT categoryID, categoryName FROM categories ORDER BY categoryName ASC");
                while($category = $categoriesResult->fetch_assoc()): ?>
                    <option value="<?= $category['categoryID'] ?>" <?= ($category['categoryID'] == $categoryID) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['categoryName']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <input type="submit" value="Update Product">
        </form>
    </div>
</body>
</html>
