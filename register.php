<?php
session_start();
require 'db.php'; // your connection file

// --- 1. Submission Handling and Validation ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // Store user input to make form "sticky" upon error
    $_SESSION['form_data'] = [
        'name'  => $name,
        'email' => $email
    ];
    
    // Check if passwords match
    if ($password !== $confirm) {
        $_SESSION['error'] = "The passwords you entered do not match. Please ensure both fields are identical.";
        header("Location: register.php"); // Redirect back to the form
        exit();
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM customer WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "This email address is already registered. Please log in instead.";
        header("Location: register.php");
        exit();
    }

    // Insert into customer (Note: Hashing the password is highly recommended in a real project!)
    $emptyAddress = "";
    $emptyPhone = "";
    $stmt = $conn->prepare("INSERT INTO customer (name, email, address, phone, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $emptyAddress, $emptyPhone, $password);

    if ($stmt->execute()) {
        unset($_SESSION['form_data']); // Clear sticky data on success
        $_SESSION['success_message'] = "Account created successfully! You can now log in.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "A database error occurred. Please try again.";
        header("Location: register.php");
        exit();
    }
}

// --- 2. Error/Data Retrieval for Display ---
// Retrieve error and clear session variable
$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : null;
unset($_SESSION['error']);

// Retrieve form data and clear session variable
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
unset($_SESSION['form_data']); 
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

            <?php if ($error_message): ?>
                <div class="alert error-alert">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" 
                       value="<?php echo htmlspecialchars($form_data['name'] ?? ''); ?>" required />
                </div>

                <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required />
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