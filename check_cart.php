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
}

// Check the request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Invalid request method.');
    exit();
}

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'] ?? null;
$product_id = $data['product_id'] ?? null;

if (!$user_id || !$product_id) {
    sendResponse('error', 'Required fields are missing.');
    exit();
}

// Query to check if the product is already in the cart
$query = "
    SELECT * FROM carts 
    WHERE user_id = ? AND product_id = ? AND cart_status = 'to be checked out'
";

// Prepare and execute the statement
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $user_id, $product_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $itemExists = $result->num_rows > 0;
    sendResponse('success', 'Product check complete.', ['exists' => $itemExists]);
} else {
    sendResponse('error', 'Failed to check product in cart.');
}

// Close the database connection
$stmt->close();
mysqli_close($conn);
?>
