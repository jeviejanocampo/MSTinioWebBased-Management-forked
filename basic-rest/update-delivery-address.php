<?php
header('Content-Type: application/json');

include 'dbcon.php'; // Include your database connection file

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
if (!isset($data['delivery_address_id']) || !is_int($data['delivery_address_id'])) {
    sendResponse('error', 'Invalid delivery address ID');
}

// Sanitize and assign the delivery address ID
$delivery_address_id = intval($data['delivery_address_id']);

// Prepare the update query to change d_status to 'unsaved'
$query = "UPDATE delivery_address SET d_status = 'unsaved' WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $delivery_address_id);

if ($stmt->execute()) {
    sendResponse('success', 'Delivery address status updated to unsaved successfully');
} else {
    sendResponse('error', 'Error updating delivery address status');
}

// Close the statement and database connection
$stmt->close();
$conn->close();
?>
