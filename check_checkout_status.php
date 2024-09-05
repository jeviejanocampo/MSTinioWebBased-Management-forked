<?php
header('Content-Type: application/json');

include 'dbcon.php';

// Function to send JSON response
function sendResponse($status, $message, $data = []) {
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

// Get the input data from the POST request
$data = json_decode(file_get_contents('php://input'), true);
$user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;

// Validate user_id
if ($user_id <= 0) {
    sendResponse('error', 'Invalid user ID');
}

// Check for existing checkout status
$sql = "SELECT * FROM checkout WHERE user_id = ? AND checkout_status IN ('Pending', 'Completed')";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    sendResponse('error', 'Database prepare statement failed');
}
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Prepare response data
$responseData = [
    'checkout_exists' => false,
    'checkout_id' => null,
    'status' => null
];

// Check if there is a pending or completed checkout
if ($result->num_rows > 0) {
    $checkout = $result->fetch_assoc();
    $responseData['checkout_exists'] = true;
    $responseData['checkout_id'] = $checkout['checkout_id'];
    $responseData['status'] = $checkout['checkout_status'];
}

// Send the response
sendResponse('success', 'Checkout status retrieved', $responseData);

// Close the database connection
$stmt->close();
$conn->close();
?>
