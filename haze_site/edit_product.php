<?php
session_start();
require_once 'database.php';

$product = null;
$error = '';

$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['error'] = 'Product not found';
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'] ?? '';
    $price = $_POST['price'] ?? '';
    $category_id = $_POST['category_id'] ?? '';

    if (empty($product_name) || empty($price) || empty($category_id)) {
        $error = 'All fields are required';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, category_id = ? WHERE id = ?");
            $stmt->execute([$product_name, $price, $category_id, $product_id]);
            $_SESSION['message'] = 'Product updated successfully!';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Error updating product: ' . $e->getMessage();
        }
    }
}

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Product</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <a href="index.php" class="btn-back">Back to Products</a>

        <section class="form-section">
            <form method="POST" action="edit_product.php?id=<?php echo $product_id; ?>" class="form">
                <div class="form-group">
                    <label for="product_name">Product Name:</label>
                    <input 
                        type="text" 
                        id="product_name" 
                        name="product_name" 
                        value="<?php echo htmlspecialchars($product['name']); ?>"
                        placeholder="Enter product name" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="price">Price:</label>
                    <input 
                        type="number" 
                        id="price" 
                        name="price" 
                        value="<?php echo htmlspecialchars($product['price']); ?>"
                        placeholder="Enter price" 
                        step="0.01" 
                        min="0" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="category_id">Category:</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Select a category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option 
                                value="<?php echo $cat['id']; ?>"
                                <?php echo ($cat['id'] == $product['category_id']) ? 'selected' : ''; ?>
                            >
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn-primary">Update Product</button>
            </form>
        </section>
    </div>
</body>
</html>
