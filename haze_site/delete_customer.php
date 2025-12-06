<?php
session_start();
require_once 'database.php';

$customer_id = $_GET['id'] ?? null;

if (!$customer_id) {
    $_SESSION['error'] = 'Customer ID not provided';
    header('Location: add_customer.php');
    exit;
}

try {

    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE customer_id = ?");
    $stmt->execute([$customer_id]);
    $result = $stmt->fetch();

    if ($result['count'] > 0) {
        $_SESSION['error'] = 'Cannot delete customer with existing orders. Delete orders first.';
    } else {

        $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
        $stmt->execute([$customer_id]);
        $_SESSION['message'] = 'Customer deleted successfully!';
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error deleting customer: ' . $e->getMessage();
}

header('Location: add_customer.php');
exit;
?>
