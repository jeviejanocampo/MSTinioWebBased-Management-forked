<?php
header('Content-Type: application/json');
include 'dbcon.php';  // Ensure this file contains your database connection logic

function sendResponse($status, $message) {
    echo json_encode([
        'status' => $status,
        'message' => $message
    ]);
    exit();
}

// Get the raw POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate the input data
if (!isset($data['payment_method']) || empty($data['payment_method'])) {
    sendResponse('error', 'Payment method is required');
}

if (!isset($data['amount_paid']) || !is_numeric($data['amount_paid'])) {
    sendResponse('error', 'Valid amount paid is required');
}

// Sanitize and assign input data to variables
$payment_method = $conn->real_escape_string($data['payment_method']);
$amount_paid = $conn->real_escape_string($data['amount_paid']);
$upload_image = isset($data['upload_image']) ? $conn->real_escape_string($data['upload_image']) : ''; // Set to empty string if not provided
$referenced_code = isset($data['referenced_code']) ? $conn->real_escape_string($data['referenced_code']) : ''; // Sanitize referenced_code
$user_id = isset($data['user_id']) ? $conn->real_escape_string($data['user_id']) : null; // Get user_id from input

// Check if user_id is provided
if ($user_id === null) {
    sendResponse('error', 'User ID is required');
}

// Fetch the order_id from the orders table where payment_method and user_id match
try {
    // Start a transaction to ensure atomicity
    $conn->autocommit(FALSE); 

    // Query to get the order_id based on payment_method and user_id
    $order_query = "SELECT order_id FROM orders WHERE payment_method = ? AND user_id = ? ORDER BY order_date DESC LIMIT 1";
    $order_stmt = $conn->prepare($order_query);

    // Check if the statement preparation was successful
    if ($order_stmt === false) {
        sendResponse('error', 'Failed to prepare order statement: ' . $conn->error);
    }

    // Bind the payment_method and user_id parameters
    $order_stmt->bind_param('si', $payment_method, $user_id); // 's' for string, 'i' for integer

    // Execute the statement
    if (!$order_stmt->execute()) {
        $conn->rollback();
        sendResponse('error', 'Failed to fetch order_id: ' . $order_stmt->error);
    }

    // Fetch the result
    $result = $order_stmt->get_result();
    if ($result->num_rows === 0) {
        sendResponse('error', 'Order not found with the specified payment method: ' . $payment_method);
    }

    // Fetch the order_id from the result
    $order = $result->fetch_assoc();
    $order_id = $order['order_id'];

    // Now check if there's already a payment entry for the same order_id
    $check_payment_query = "SELECT * FROM payments WHERE order_id = ?";
    $check_stmt = $conn->prepare($check_payment_query);
    $check_stmt->bind_param('i', $order_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        sendResponse('error', 'Payment already exists for order_id: ' . $order_id);
    }

    // Now insert the payment details into the payments table including user_id and referenced_code
    $query = "INSERT INTO payments (order_id, payment_method, amount_paid, upload_imaged, referenced_code, user_id) 
              VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    
    // Check if the statement preparation was successful
    if ($stmt === false) {
        sendResponse('error', 'Failed to prepare payment statement: ' . $conn->error);
    }

    // Use 'issssi' for parameter binding: 'i' for integer (order_id, user_id) and 's' for strings
    $stmt->bind_param('issssi', $order_id, $payment_method, $amount_paid, $upload_image, $referenced_code, $user_id);
    
    // Execute the statement
    if (!$stmt->execute()) {
        // Rollback the transaction on execution error
        $conn->rollback();
        sendResponse('error', 'Failed to insert payment: ' . $stmt->error);
    }

    // Commit the transaction if successful
    $conn->commit(); 

    sendResponse('success', 'Payment inserted successfully with order_id: ' . $order_id);
} catch (Exception $e) {
    // Rollback the transaction on error
    $conn->rollback(); 
    sendResponse('error', 'Failed to insert payment: ' . $e->getMessage());
} finally {
    // Close the statements and connection
    if (isset($order_stmt)) {
        $order_stmt->close();
    }
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($check_stmt)) {
        $check_stmt->close();
    }
    $conn->close();
}
?>
