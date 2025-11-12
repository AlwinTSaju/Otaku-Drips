<?php
session_start();
require 'db.php'; // database connection

if (!isset($_SESSION['customer_id']) || empty($_SESSION['cart']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: shop.php');
    exit;
}

$customer_id = $_SESSION['customer_id'];
$cart = $_SESSION['cart'];
$grand_total = $_POST['grand_total'];
$payment_method = $_POST['payment_method'];

// Start a transaction for reliability
$conn->begin_transaction();

try {
    // 1. Insert into orders table
    $stmt_order = $conn->prepare("INSERT INTO orders (customer_id, order_date, status) VALUES (?, NOW(), 'Processing')");
    $stmt_order->bind_param("i", $customer_id);
    $stmt_order->execute();
    $order_id = $stmt_order->insert_id;
    $stmt_order->close();

    // 2. Insert each cart item into order_item table
    $stmt_item = $conn->prepare("INSERT INTO order_item (order_id, product_id, quantity) VALUES (?, ?, ?)");
    foreach ($cart as $item) {
        $stmt_item->bind_param("iii", $order_id, $item['product_id'], $item['qty']);
        $stmt_item->execute();
    }
    $stmt_item->close();
    
    // --- NEW PAYMENT LOGIC ---

    // 3. Insert random/dummy details into payment_gateway
    $gateway_name = ucfirst($payment_method) . " Payment Gateway";
    // Simple mock API endpoint
    $gateway_endpoint = "https://mock.api.otakudrips.com/pay/" . strtolower($payment_method); 
    
    // NOTE: Assuming your payment_gateway table has (name, api_endpoint)
    $stmt_gateway = $conn->prepare("INSERT INTO payment_gateway (name, api_endpoint) VALUES (?, ?)");
    $stmt_gateway->bind_param("ss", $gateway_name, $gateway_endpoint);
    $stmt_gateway->execute();
    $gateway_id = $conn->insert_id;
    $stmt_gateway->close();
    
    // 4. Insert into payment table
    // Status is 'Pending' for COD, 'Completed' for others (simulating success)
    $payment_status = ($payment_method === 'cod') ? 'Pending' : 'Completed'; 
    
    // NOTE: Inserting into payment table with the required minimum fields
    $stmt_payment = $conn->prepare("INSERT INTO payment (order_id, payment_date, amount, status) VALUES (?, NOW(), ?, ?)");
    $stmt_payment->bind_param("ids", $order_id, $grand_total, $payment_status);
    $stmt_payment->execute();
    $stmt_payment->close();

    // 5. Commit the transaction
    $conn->commit();
    
    // 6. Clear cart
    $_SESSION['cart'] = [];

    // 7. Redirect to order success page
    header("Location: order-success.php?order_id=$order_id");
    exit;
    
} catch (Exception $e) {
    // Rollback changes if any step failed
    $conn->rollback();
    
    // Log the error for debugging
    error_log("Checkout Transaction Failed: " . $e->getMessage()); 
    
    // Inform the user
    $_SESSION['error'] = "An error occurred during payment processing. Please try again.";
    header("Location: checkout.php");
    exit;
}

?>