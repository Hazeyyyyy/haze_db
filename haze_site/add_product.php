<?php
include 'database.php';

$categories = $conn->query("SELECT * FROM categories");

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO products (name, category_id, price, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sids", $name, $category_id, $price, $description); 

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Product</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1 style="text-align:center;">Add Product</h1>
    <form method="post">
        <label>Name:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Category:</label><br>
        <select name="category_id" required>
            <option value="">Select Category</option>
            <?php while($cat = $categories->fetch_assoc()): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Price:</label><br>
        <input type="number" name="price" step="0.01" required><br><br>

        <label>Description:</label><br>
        <textarea name="description"></textarea><br><br>

        <div class="button-container">
            <a href="index.php" class="button">Add Product</a>
        </div>
    </form>
    <br>
    <div class="button-container">
            <a href="index.php" class="button">Back to Dashboard</a>
        </div>
</div>
</body>
</html>