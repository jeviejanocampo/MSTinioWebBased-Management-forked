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

$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$checkout_id = isset($_POST['checkout_id']) ? intval($_POST['checkout_id']) : 0;

if ($user_id <= 0 || $checkout_id <= 0) {
    sendResponse('error', 'Invalid user ID or checkout ID');
}

$query = "DELETE FROM cart WHERE user_id = ? AND checkout_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $checkout_id);

if ($stmt->execute()) {
    sendResponse('success', 'Cart items deleted successfully');
} else {
    sendResponse('error', 'Error executing query');
}
?>
