<?php
session_start();
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = $_POST['customer_name'] ?? '';
    $email = $_POST['email'] ?? '';

    if (empty($customer_name)) {
        $_SESSION['error'] = 'Customer name is required';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO customers (name, email) VALUES (?, ?)");
            $stmt->execute([$customer_name, $email]);
            $_SESSION['message'] = 'Customer added successfully!';
            header('Location: add_customer.php');
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Error adding customer: ' . $e->getMessage();
        }
    }
}

$customers = $pdo->query("SELECT * FROM customers ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Customer</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Manage Customers</h1>

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
            <h2>Add New Customer</h2>
            <form method="POST" action="add_customer.php" class="form">
                <div class="form-group">
                    <label for="customer_name">Customer Name:</label>
                    <input 
                        type="text" 
                        id="customer_name" 
                        name="customer_name" 
                        placeholder="Enter customer name" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email (optional):</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="Enter email address"
                    >
                </div>

                <button type="submit" class="btn-primary">Add Customer</button>
            </form>
        </section>

        <?php if (count($customers) > 0): ?>
            <section class="list-section">
                <h2>All Customers</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Customer ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $cust): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cust['id']); ?></td>
                                <td><?php echo htmlspecialchars($cust['name']); ?></td>
                                <td><?php echo htmlspecialchars($cust['email'] ?? 'N/A'); ?></td>
                                <td>
                                    <a href="edit_customer.php?id=<?php echo $cust['id']; ?>" class="btn-edit">Edit</a>
                                    <a href="delete_customer.php?id=<?php echo $cust['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
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