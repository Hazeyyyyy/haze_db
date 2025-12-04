<?php
include 'database.php'; // mysqli connection

// Initialize variables
$categoryName = "";
$error = $success = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoryName = trim($_POST['categoryName']);

    if (empty($categoryName)) {
        $error = "Category name is required.";
    } else {
        // Insert category using prepared statement
        $stmt = $conn->prepare("INSERT INTO categories (categoryName) VALUES (?)");
        $stmt->bind_param("s", $categoryName);

        if ($stmt->execute()) {
            $success = "Category added successfully!";
            $categoryName = "";
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
    <title>Add Category</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Add New Category</h1>

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

            <label for="categoryName">Category Name:</label>
            <input type="text" name="categoryName" id="categoryName" value="<?= htmlspecialchars($categoryName) ?>" required>

            <input type="submit" value="Add Category">
        </form>
    </div>
</body>
</html>
