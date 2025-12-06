<?php
session_start();
require_once 'database.php';

$customer_id = $_GET['id'] ?? null;
$error = '';

if (!$customer_id) {
    header('Location: add_customer.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$customer_id]);
$customer = $stmt->fetch();

if (!$customer) {
    $_SESSION['error'] = 'Customer not found';
    header('Location: add_customer.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = $_POST['customer_name'] ?? '';
    $email = $_POST['email'] ?? '';

    if (empty($customer_name)) {
        $error = 'Customer name is required';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE customers SET name = ?, email = ? WHERE id = ?");
            $stmt->execute([$customer_name, $email, $customer_id]);
            $_SESSION['message'] = 'Customer updated successfully!';
            header('Location: add_customer.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Error updating customer: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Customer</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <a href="add_customer.php" class="btn-back">Back to Customers</a>

        <section class="form-section">
            <form method="POST" action="edit_customer.php?id=<?php echo $customer_id; ?>" class="form">
                <div class="form-group">
                    <label for="customer_name">Customer Name:</label>
                    <input 
                        type="text" 
                        id="customer_name" 
                        name="customer_name" 
                        value="<?php echo htmlspecialchars($customer['name']); ?>"
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
                        value="<?php echo htmlspecialchars($customer['email'] ?? ''); ?>"
                        placeholder="Enter email address"
                    >
                </div>

                <button type="submit" class="btn-primary">Update Customer</button>
            </form>
        </section>
    </div>
</body>
</html>