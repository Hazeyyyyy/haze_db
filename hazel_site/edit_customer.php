<?php
include 'database.php';

$error = $success = "";
$customerName = $email = "";
$id = $_GET['id'] ?? 0;

if (!$id) {
    header("Location: add_customer.php");
    exit;
}

// Fetch customer details
$stmt = $conn->prepare("SELECT customerName, email FROM customers WHERE customerID=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($customerName, $email);
$stmt->fetch();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customerName = $_POST['customerName'];
    $email = $_POST['email'];

    if (empty($customerName)) {
        $error = "Customer name cannot be empty.";
    } else {
        $stmt = $conn->prepare("UPDATE customers SET customerName=?, email=? WHERE customerID=?");
        $stmt->bind_param("ssi", $customerName, $email, $id);
        if ($stmt->execute()) {
            $success = "Customer updated successfully!";
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Customer</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Edit Customer</h1>

<div class="nav-card">
    <a href="add_customer.php">Back to Add Customer</a>
    <a href="index.php">Products</a>
    <a href="add_product.php">Add Product</a>
    <a href="manage_categories.php">Manage Categories</a>
</div>

<div class="form-container">
    <form method="POST" action="">
        <?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
        <?php if($success) echo "<p style='color:green;'>$success</p>"; ?>

        <label for="customerName">Customer Name:</label>
        <input type="text" name="customerName" id="customerName" value="<?= htmlspecialchars($customerName) ?>" required>

        <label for="email">Email:</label>
        <input type="text" name="email" id="email" value="<?= htmlspecialchars($email) ?>">

        <input type="submit" value="Update Customer">
    </form>
</div>
</body>
</html>
