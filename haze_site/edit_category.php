<?php
session_start();
require_once 'database.php';

$category_id = $_GET['id'] ?? null;
$error = '';

if (!$category_id) {
    header('Location: add_category.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch();

if (!$category) {
    $_SESSION['error'] = 'Category not found';
    header('Location: add_category.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = $_POST['category_name'] ?? '';

    if (empty($category_name)) {
        $error = 'Category name is required';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
            $stmt->execute([$category_name, $category_id]);
            $_SESSION['message'] = 'Category updated successfully!';
            header('Location: add_category.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Error updating category: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Category</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <a href="add_category.php" class="btn-back">Back to Categories</a>

        <section class="form-section">
            <form method="POST" action="edit_category.php?id=<?php echo $category_id; ?>" class="form">
                <div class="form-group">
                    <label for="category_name">Category Name:</label>
                    <input 
                        type="text" 
                        id="category_name" 
                        name="category_name" 
                        value="<?php echo htmlspecialchars($category['name']); ?>"
                        placeholder="Enter category name" 
                        required
                    >
                </div>

                <button type="submit" class="btn-primary">Update Category</button>
            </form>
        </section>
    </div>
</body>
</html>