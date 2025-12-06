<?php
session_start();
require_once 'database.php';

$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    $_SESSION['error'] = 'Product ID not provided';
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM order_items WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $result = $stmt->fetch();

    if ($result['count'] > 0) {
        $_SESSION['error'] = 'Cannot delete product with existing orders. This product is referenced in orders.';
    } else {

        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $_SESSION['message'] = 'Product deleted successfully!';
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error deleting product: ' . $e->getMessage();
}

header('Location: index.php');
exit;
?>
