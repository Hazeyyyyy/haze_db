<?php
session_start();
require_once 'database.php';

$category_id = $_GET['id'] ?? null;

if (!$category_id) {
    $_SESSION['error'] = 'Category ID not provided';
    header('Location: add_category.php');
    exit;
}

try {

    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $_SESSION['message'] = 'Category deleted successfully!';
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error deleting category: ' . $e->getMessage();
}

header('Location: add_category.php');
exit;
?>
