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
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse('error', 'Invalid request method.');
    exit();
}

// Extract user_id from query parameters
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if (!$user_id) {
    sendResponse('error', 'User ID is required.');
    exit();
}

// Query to fetch the total quantity of products in the cart for the user
$query = "
    SELECT 
        SUM(c.product_quantity) AS total_quantity
    FROM 
        carts c
    WHERE 
        c.user_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the query was successful
if (!$result) {
    sendResponse('error', 'Database query failed.');
    exit();
}

// Fetch the data
$data = $result->fetch_assoc();
$total_quantity = $data['total_quantity'] ?? 0;

// Send a successful response
sendResponse('success', 'Cart items count fetched successfully.', ['total_quantity' => $total_quantity]);

// Close the database connection
$stmt->close();
$conn->close();
?>
