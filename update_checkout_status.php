<?php
header('Content-Type: application/json');

include 'dbcon.php';

function sendResponse($status, $message) {
    echo json_encode([
        'status' => $status,
        'message' => $message
    ]);
    exit();
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['checkout_id']) || !is_int($data['checkout_id'])) {
    sendResponse('error', 'Invalid checkout ID');
}

$checkout_id = intval($data['checkout_id']); // Convert to integer

$query = "UPDATE checkout SET checkout_status = 'Completed' WHERE checkout_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $checkout_id);

if ($stmt->execute()) {
    sendResponse('success', 'Checkout status updated successfully');
} else {
    sendResponse('error', 'Error updating checkout status');
}
?>
