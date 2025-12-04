<?php
include 'database.php'; // mysqli connection

// Initialize variables
$productName = $price = $categoryID = "";
$error = $success = "";

// Fetch categories for dropdown
$categoriesResult = $conn->query("SELECT categoryID, categoryName FROM categories ORDER BY categoryName ASC");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productName = trim($_POST['productName']);
    $price = trim($_POST['price']);
    $categoryID = $_POST['categoryID'] ?: null;

    // Simple validation
    if (empty($productName) || empty($price)) {
        $error = "Product name and price are required.";
    } else {
        // Insert product using prepared statement
        $stmt = $conn->prepare("INSERT INTO products (productName, price, categoryID) VALUES (?, ?, ?)");
        if ($categoryID === null) {
            $stmt->bind_param("sdi", $productName, $price, $categoryID);
        } else {
            $categoryID = (int)$categoryID;
            $stmt->bind_param("sdi", $productName, $price, $categoryID);
        }

        if ($stmt->execute()) {
            $success = "Product added successfully!";
            $productName = $price = $categoryID = "";
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
    <title>Add Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Add New Product</h1>

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

            <label for="productName">Product Name:</label>
            <input type="text" name="productName" id="productName" value="<?= htmlspecialchars($productName) ?>" required>

            <label for="price">Price:</label>
            <input type="number" step="0.01" name="price" id="price" value="<?= htmlspecialchars($price) ?>" required>

            <label for="categoryID">Category:</label>
            <select name="categoryID" id="categoryID">
                <option value="">-- Select Category --</option>
                <?php while($category = $categoriesResult->fetch_assoc()): ?>
                    <option value="<?= $category['categoryID'] ?>" <?= ($category['categoryID'] == $categoryID) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['categoryName']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <input type="submit" value="Add Product">
        </form>
    </div>
</body>
</html>
