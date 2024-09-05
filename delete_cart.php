<?php
header('Content-Type: application/json');

include 'dbcon.php';

function sendResponse($status, $message) {
    echo json_encode([
        'status' => $status,
        'message' => $message
    ]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Invalid request method.');
    exit();
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['user_id']) || empty($data['user_id']) || !isset($data['product_id']) || empty($data['product_id'])) {
    sendResponse('error', 'User ID and Product ID are required.');
    exit();
}

$user_id = intval($data['user_id']);
$product_id = intval($data['product_id']);

$query = "DELETE FROM carts WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $product_id);

if ($stmt->execute()) {
    sendResponse('success', 'Cart item(s) deleted successfully.');
} else {
    sendResponse('error', 'Failed to delete cart item(s).');
}

$stmt->close();
$conn->close();
?>
