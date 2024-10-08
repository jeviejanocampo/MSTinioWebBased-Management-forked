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

// Get the input data from the request
$input = json_decode(file_get_contents('php://input'), true);

// Validate input data
if (!isset($input['items']) || !is_array($input['items'])) {
    sendResponse('error', 'Invalid input data.');
}

// Extract items from input
$items = $input['items'];

// Build query to get user_id from the carts table
$placeholders = implode(',', array_fill(0, count($items), '(?, ?)'));
$query = "SELECT DISTINCT user_id FROM carts WHERE (product_id, product_quantity) IN ($placeholders)";

// Prepare and execute the query
$stmt = $conn->prepare($query);

// Bind parameters
$params = [];
foreach ($items as $item) {
    $params[] = $item['product_id'];
    $params[] = $item['product_quantity'];
}

$stmt->bind_param(str_repeat('ii', count($items)), ...$params);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    sendResponse('success', 'User ID fetched successfully.', ['user_id' => $user['user_id']]);
} else {
    sendResponse('error', 'No user found for the given items.');
}

// Close the database connection
$stmt->close();
$conn->close();
?>
