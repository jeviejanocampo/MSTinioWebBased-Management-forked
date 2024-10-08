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

if (!isset($data['amount_paid']) || empty($data['amount_paid'])) {
    sendResponse('error', 'Amount paid is required');
}

if (!isset($data['referenced_code']) || empty($data['referenced_code'])) {
    sendResponse('error', 'Referenced code is required');
}

// Sanitize and assign input data to variables
$payment_method = $conn->real_escape_string($data['payment_method']);
$amount_paid = $conn->real_escape_string($data['amount_paid']);
$referenced_code = $conn->real_escape_string($data['referenced_code']);
$upload_image = isset($data['upload_image']) ? $conn->real_escape_string($data['upload_image']) : ''; // Set to empty string if not provided

// Insert the payment details into the payments table
try {
    // Start a transaction to ensure atomicity
    $conn->autocommit(FALSE); 

    $query = "INSERT INTO payments (payment_method, amount_paid, referenced_code, upload_imaged) 
              VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    
    // Check if the statement preparation was successful
    if ($stmt === false) {
        sendResponse('error', 'Failed to prepare statement: ' . $conn->error);
    }

    // Use 'ssss' for all string types
    $stmt->bind_param('ssss', $payment_method, $amount_paid, $referenced_code, $upload_image);
    
    // Execute the statement
    if (!$stmt->execute()) {
        // Rollback the transaction on execution error
        $conn->rollback();
        sendResponse('error', 'Failed to insert payment: ' . $stmt->error);
    }

    // Commit the transaction if successful
    $conn->commit(); 

    sendResponse('success', 'Payment inserted successfully');
} catch (Exception $e) {
    // Rollback the transaction on error
    $conn->rollback(); 
    sendResponse('error', 'Failed to insert payment: ' . $e->getMessage());
} finally {
    // Close the statement and connection
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>
