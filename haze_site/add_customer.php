<?php
include 'database.php';

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("INSERT INTO customers (name, email, phone, address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $address);

    if ($stmt->execute()) {
        $message = "Customer added successfully!";
        $message_class = "success";
    } else {
        $message = "Error: " . $stmt->error;
        $message_class = "error";
    }

    $stmt->close();
}
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
        <div align="center">
    <h1>Add Customer</h1>
</div>
    
        <?php if(isset($message)): ?>
            <p class="<?= $message_class ?>"><?= $message ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <label>Name:</label>
            <input type="text" name="name" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Phone:</label>
            <input type="text" name="phone">

            <label>Address:</label>
            <textarea name="address"></textarea>

            <div style="text-align: center; margin-top: 20px;">
    <button type="submit" class="btn btn-primary">Add Customer</button>
</div>

        </form>
    </div>
    <div class="button-container">
            <a href="index.php" class="button">Back to Dashboard</a>
        </div>
</body>
</html>