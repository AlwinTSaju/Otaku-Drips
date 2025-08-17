<?php
session_start();
require 'db.php'; // your connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm) {
        die("Passwords do not match.");
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM customer WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die("Email already registered. Please login.");
    }

    // Insert into customer (address and phone kept empty for now)
    $stmt = $conn->prepare("INSERT INTO customer (name, email, address, phone, password) VALUES (?, ?, ?, ?, ?)");
    $emptyAddress = "";
    $emptyPhone = "";
    $stmt->bind_param("sssss", $name, $email, $emptyAddress, $emptyPhone, $password);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - OtakuDrips</title>
  <link rel="stylesheet" href="styles/register.css" />
</head>
<body>
    <div class="register-background">
        <video autoplay muted loop>
            <source src="./images/boat.mp4" type="video/mp4">
        </video>
        <div class="register-container">
            <div class="register-card">
            <h2>Create Account</h2>
            <p class="subtitle">Join OtakuDrips and start shopping</p>
            <form action="register.php" method="POST">
                <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required />
                </div>

                <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required />
                </div>

                <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required />
                </div>

                <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required />
                </div>

                <button type="submit" class="register-btn">Sign Up</button>
            </form>
            <p class="login-link">Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
  </div>
</body>
</html>
