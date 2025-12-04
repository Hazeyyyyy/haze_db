<?php
include 'database.php'; // mysqli connection

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Product ID is missing.");
}

$productID = (int)$_GET['id'];

// Delete the product
$stmt = $conn->prepare("DELETE FROM products WHERE productID = ?");
$stmt->bind_param("i", $productID);

if ($stmt->execute()) {
    $stmt->close();
    // Redirect back to the main product list
    header("Location: index.php");
    exit();
} else {
    echo "Error deleting product: " . $stmt->error;
    $stmt->close();
}
?>
