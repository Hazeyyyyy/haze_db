<?php
include 'database.php';

if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();
}

$categories = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Categories</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1 align="center">Manage Categories</h1>
    <form method="post">
        <input type="text" name="name" placeholder="Category Name" required>
        <div class="button-container">
            <a href="index.php" class="button">Add Category</a>
        </div>
    </form>

    <h2>Existing Categories</h2>
    <ul>
        <?php while($row = $categories->fetch_assoc()): ?>
            <li><?= htmlspecialchars($row['name']) ?></li>
        <?php endwhile; ?>
    </ul>
    <br>
    <div class="button-container">
            <a href="index.php" class="button">Back to Dashboard</a>
        </div>
</div>
</body>
</html>