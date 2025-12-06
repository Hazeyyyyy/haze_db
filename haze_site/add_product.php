<?php
session_start();
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'] ?? '';
    $price = $_POST['price'] ?? '';
    $category_id = $_POST['category_id'] ?? '';

    if (empty($product_name) || empty($price) || empty($category_id)) {
        $_SESSION['error'] = 'All fields are required';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, price, category_id) VALUES (?, ?, ?)");
            $stmt->execute([$product_name, $price, $category_id]);
            $_SESSION['message'] = 'Product added successfully!';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Error adding product: ' . $e->getMessage();
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
    <title>Add Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Add New Product</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <a href="index.php" class="btn-back">Back to Products</a>

        <section class="form-section">
            <form method="POST" action="add_product.php" class="form">
                <div class="form-group">
                    <label for="product_name">Product Name:</label>
                    <input 
                        type="text" 
                        id="product_name" 
                        name="product_name" 
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
                            <option value="<?php echo $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (count($categories) == 0): ?>
                        <p class="error-text">No categories found. <a href="add_category.php">Create a category first</a></p>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-primary">Add Product</button>
            </form>
        </section>
    </div>
</body>
</html>