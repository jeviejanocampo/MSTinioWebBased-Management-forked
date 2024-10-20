<?php
header('Content-Type: application/json');

include 'dbcon.php';

function sendResponse($status, $message, $data = []) {
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'carts' => $data
    ]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse('error', 'Invalid request method.');
    exit();
}

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

if (!$user_id) {
    sendResponse('error', 'User ID is required.');
    exit();
}

$query = "
    SELECT 
        c.cart_id, 
        c.product_id, 
        c.user_id, 
        c.product_quantity, 
        c.product_price, 
        p.product_name
    FROM 
        carts c
    JOIN 
        products p 
    ON 
        c.product_id = p.product_id
    WHERE 
        c.user_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    sendResponse('error', 'Database query failed.');
    exit();
}

$carts = [];
while ($row = $result->fetch_assoc()) {
    $carts[] = $row;
}

sendResponse('success', 'Carts fetched successfully.', $carts);

$stmt->close();
$conn->close();
?>
