<?php
header('Content-Type: application/json');

// Include database connection
include 'dbcon.php';

// Function to send JSON response
function sendResponse($status, $message, $data = []) {
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit(); // Ensure no further output
}

// Check the request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Invalid request method.');
}

// Get the input data from the request
$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['user_id']) || empty($input['total_amount']) || empty($input['items']) || empty($input['coordinates']) || empty($input['payment_method'])) {
    sendResponse('error', 'Missing required fields.');
}

// Extract input data
$user_id = $input['user_id'];
$total_amount = $input['total_amount'];
$items = $input['items'];
$coordinates = $input['coordinates'];
$payment_method = $input['payment_method']; // Get the payment method from the input

mysqli_autocommit($conn, false); // Start transaction

try {
    // Insert into checkout table
    $stmt = $conn->prepare("INSERT INTO checkout (user_id, total_amount, latitude, longitude) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iddd", $user_id, $total_amount, $coordinates['latitude'], $coordinates['longitude']);
    if (!$stmt->execute()) {
        throw new Exception('Failed to insert into checkout table.');
    }
    $checkout_id = $stmt->insert_id; // Get the ID of the newly inserted checkout

    // Insert into checkout_details table
    $stmt = $conn->prepare("INSERT INTO checkout_details (checkout_id, cart_id, product_id, product_quantity, product_price) VALUES (?, ?, ?, ?, ?)");
    foreach ($items as $item) {
        $stmt->bind_param("iiidi", $checkout_id, $item['cart_id'], $item['product_id'], $item['product_quantity'], $item['product_price']);
        if (!$stmt->execute()) {
            throw new Exception('Failed to insert into checkout_details table.');
        }
    }

    // Insert into orders table with payment method
    $stmt = $conn->prepare("INSERT INTO orders (checkout_id, user_id, payment_method) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $checkout_id, $user_id, $payment_method); // Insert payment_method
    if (!$stmt->execute()) {
        throw new Exception('Failed to insert into orders table.');
    }

    // Delete from carts table
    $stmt = $conn->prepare("DELETE FROM carts WHERE cart_id IN (SELECT cart_id FROM checkout_details WHERE checkout_id = ?)");
    $stmt->bind_param("i", $checkout_id);
    if (!$stmt->execute()) {
        throw new Exception('Failed to delete from carts table.');
    }

    mysqli_commit($conn); // Commit transaction
    sendResponse('success', 'Order placed successfully.');
} catch (Exception $e) {
    mysqli_rollback($conn); // Rollback transaction
    sendResponse('error', $e->getMessage());
}
?>
