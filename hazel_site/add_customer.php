<?php
include 'database.php'; // mysqli connection

// Initialize variables
$customerName = $email = "";
$error = $success = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customerName = trim($_POST['customerName']);
    $email = trim($_POST['email']);

    if (empty($customerName)) {
        $error = "Customer name is required.";
    } else {
        // Insert customer using prepared statement
        $stmt = $conn->prepare("INSERT INTO customers (customerName, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $customerName, $email);

        if ($stmt->execute()) {
            $success = "Customer added successfully!";
            $customerName = $email = "";
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
    <title>Add Customer</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Add New Customer</h1>

    <!-- Navigation Card -->
    <div class="nav-card">
        <a href="add_product.php">Add Product</a>
        <a href="add_categories.php">Add Categories</a>
        <a href="add_customer.php">Add Customer</a>
        <a href="add_order.php">Add Orders</a>
    </div>

    <!-- Form Container -->
    <div class="form-container">
        <form method="POST" action="">
            <?php if($error): ?>
                <p style="color:red; text-align:center; margin-bottom:15px;"><?= $error ?></p>
            <?php endif; ?>
            <?php if($success): ?>
                <p style="color:green; text-align:center; margin-bottom:15px;"><?= $success ?></p>
            <?php endif; ?>

            <label for="customerName">Customer Name:</label>
            <input type="text" name="customerName" id="customerName" value="<?= htmlspecialchars($customerName) ?>" required>

            <label for="email">Email (optional):</label>
            <input type="text" name="email" id="email" value="<?= htmlspecialchars($email) ?>">

            <input type="submit" value="Add Customer">
        </form>
    </div>
</body>
</html>
