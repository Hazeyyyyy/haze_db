<?php
include 'database.php';

$error = $success = "";
$categoryName = "";
$id = $_GET['id'] ?? 0;

if (!$id) {
    header("Location: manage_categories.php");
    exit;
}

// Fetch category details
$stmt = $conn->prepare("SELECT categoryName FROM categories WHERE categoryID=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($categoryName);
$stmt->fetch();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoryName = $_POST['categoryName'];

    if (empty($categoryName)) {
        $error = "Category name cannot be empty.";
    } else {
        $stmt = $conn->prepare("UPDATE categories SET categoryName=? WHERE categoryID=?");
        $stmt->bind_param("si", $categoryName, $id);
        if ($stmt->execute()) {
            $success = "Category updated successfully!";
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
    <title>Edit Category</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Edit Category</h1>

<div class="nav-card">
    <a href="manage_categories.php">Back to Categories</a>
    <a href="index.php">Products</a>
    <a href="add_product.php">Add Product</a>
</div>

<div class="form-container">
    <form method="POST" action="">
        <?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
        <?php if($success) echo "<p style='color:green;'>$success</p>"; ?>

        <label for="categoryName">Category Name:</label>
        <input type="text" name="categoryName" id="categoryName" value="<?= htmlspecialchars($categoryName) ?>" required>

        <input type="submit" value="Update Category">
    </form>
</div>
</body>
</html>