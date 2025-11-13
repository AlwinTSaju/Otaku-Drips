<?php
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $msg = trim($_POST['message'] ?? '');

    // Validate inputs
    if (empty($name) || empty($email) || empty($subject) || empty($msg)) {
        $message = "All fields are required!";
        $message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address!";
        $message_type = 'error';
    } else {
        // Prepare email
        $to = "alwintsaju@gmail.com";
        $email_subject = "Contact Form: " . htmlspecialchars($subject);
        
        $body = "You have received a new contact form submission:\n\n";
        $body .= "Name: " . htmlspecialchars($name) . "\n";
        $body .= "Email: " . htmlspecialchars($email) . "\n";
        $body .= "Subject: " . htmlspecialchars($subject) . "\n";
        $body .= "Message:\n" . htmlspecialchars($msg) . "\n";
        
        // Email headers
        $headers = "From: " . htmlspecialchars($email) . "\r\n";
        $headers .= "Reply-To: " . htmlspecialchars($email) . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        // Send email
        if (mail($to, $email_subject, $body, $headers)) {
            $message = "Thank you! Your message has been sent successfully.";
            $message_type = 'success';
            $name = $email = $subject = $msg = ''; // Clear form
        } else {
            $message = "Sorry, there was an error sending your message. Please try again later.";
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us - Otaku Drips</title>
  <link rel="stylesheet" href="styles/home.css">
  <link rel="stylesheet" href="styles/shop.css">
  <style>
    body {
        background-image: url(./images/tokyo.jpg);
        background-size: cover;
    }
    .contact-container {
      max-width: 900px;
      margin: 4rem auto;
      padding: 2rem;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    .contact-container h2 {
      font-size: 2rem;
      margin-bottom: 1.5rem;
      text-align: center;
    }

    .message {
      padding: 1rem;
      margin-bottom: 1.5rem;
      border-radius: 4px;
      text-align: center;
      font-weight: 500;
    }

    .message.success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .message.error {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .contact-form {
      display: flex;
      flex-direction: column;
      gap: 1.2rem;
    }

    .contact-form input,
    .contact-form textarea {
      padding: 1rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1rem;
      width: 100%;
      box-sizing: border-box;
    }

    .contact-form textarea {
      resize: vertical;
      min-height: 150px;
    }

    .contact-form button {
      background: #ffcc00;
      border: none;
      color: #000;
      font-weight: bold;
      padding: 1rem;
      cursor: pointer;
      border-radius: 4px;
      transition: all 0.3s ease;
    }

    .contact-form button:hover {
      background: #ffdb4d;
    }

    .contact-info {
      margin-top: 3rem;
      text-align: center;
      color: #444;
    }

    .contact-info p {
      margin-bottom: 0.5rem;
    }
  </style>
</head>
<body>

  <?php include 'includes/header.php'; ?>

  <!-- Main Contact Section -->
  <main class="contact-container">
    <h2>Contact Otaku Drips</h2>
    
    <?php if ($message): ?>
      <div class="message <?php echo $message_type; ?>">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>

    <form class="contact-form" method="POST" action="contact.php">
      <input 
        type="text" 
        name="name" 
        placeholder="Your Name" 
        value="<?php echo htmlspecialchars($name ?? ''); ?>"
        required 
      />
      <input 
        type="email" 
        name="email" 
        placeholder="Your Email" 
        value="<?php echo htmlspecialchars($email ?? ''); ?>"
        required 
      />
      <input 
        type="text" 
        name="subject" 
        placeholder="Subject" 
        value="<?php echo htmlspecialchars($subject ?? ''); ?>"
        required 
      />
      <textarea 
        name="message" 
        placeholder="Your Message..." 
        required><?php echo htmlspecialchars($msg ?? ''); ?></textarea>
      <button type="submit">Send Message</button>
    </form>

    <div class="contact-info">
      <p>Email: support@otakudrips.com</p>
      <p>Phone: +91 91880 75755</p>
      <p>Instagram: @otakudrips</p>
    </div>
  </main>

  <!-- Footer -->
  <footer class="footer">
  <div class="footer-container">
    <div class="footer-brand">
      <h2>Otaku Drips</h2>
      <p>Unleash your anime style.</p>
    </div>

    <div class="footer-links">
      <div>
        <h4>Shop</h4>
        <ul>
          <li><a href="shop.php#all">All Products</a></li>
          <li><a href="order-tracking.php">Order Tracking</a></li>
        </ul>
      </div>

      <div>
        <h4>Support</h4>
        <ul>
          <li><a href="contact.php">Contact</a></li>
          <li><a href="shipping-info.html">Shipping Info</a></li>
          <li><a href="return-policy.html">Return Policy</a></li>
        </ul>
      </div>

      <div>
        <h4>Legal</h4>
        <ul>
          <li><a href="privacy.html">Privacy Policy</a></li>
          <li><a href="terms.html">Terms & Conditions</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <p>© 2025 Otaku Drips. All Rights Reserved.</p>
  </div>
</footer>

</body>
</html>
