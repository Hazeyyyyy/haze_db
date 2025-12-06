<?php
session_start();
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = $_POST['category_name'] ?? '';

    if (empty($category_name)) {
        $_SESSION['error'] = 'Category name is required';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$category_name]);
            $_SESSION['message'] = 'Category added successfully!';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Error adding category: ' . $e->getMessage();
        }
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Manage Categories</h1>

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
            <h2>Add New Category</h2>
            <form method="POST" action="add_category.php" class="form">
                <div class="form-group">
                    <label for="category_name">Category Name:</label>
                    <input 
                        type="text" 
                        id="category_name" 
                        name="category_name" 
                        placeholder="Enter category name" 
                        required
                    >
                </div>
                <button type="submit" class="btn-primary">Add Category</button>
            </form>
        </section>

        <?php if (count($categories) > 0): ?>
            <section class="list-section">
                <h2>Existing Categories</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Category ID</th>
                            <th>Category Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cat['id']); ?></td>
                                <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                <td>
                                    <a href="edit_category.php?id=<?php echo $cat['id']; ?>" class="btn-edit">Edit</a>
                                    <a href="delete_category.php?id=<?php echo $cat['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        <?php endif; ?>
    </div>
</body>
</html>