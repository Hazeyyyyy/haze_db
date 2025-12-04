<?php
include 'database.php';
$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

$categories = $conn->query("SELECT * FROM categories");

if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE products SET name=?, category_id=?, price=?, quantity=?, description=? WHERE id=?");
    $stmt->bind_param("sidisi", $name, $category_id, $price, $quantity, $description, $id);
    if($stmt->execute()){
        header("Location: index.php");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Product</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1 align="center">Edit Product</h1>
    <form method="post">
        <label>Name:</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br>

        <label>Category:</label><br>
        <select name="category_id" required>
            <?php while($cat = $categories->fetch_assoc()): ?>
                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
            <?php endwhile; ?>
        </select><br>

        <label>Price:</label><br>
        <input type="number" name="price" step="0.01" value="<?= $product['price'] ?>" required><br>

        <label>Description:</label><br>
        <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea><br><br>

        <button type="submit" name="submit" class="button">Update Product</button>
    </form>
    <br>
    <div style="text-align: center; margin-top: 20px;">
    <button type="submit" name="submit" class="button">Back to Dashboard</button>
</div>

</div>
</body>
</html>