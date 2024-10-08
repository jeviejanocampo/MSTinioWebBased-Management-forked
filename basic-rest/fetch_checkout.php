<?php
header('Content-Type: application/json');

include 'dbcon.php';

function sendResponse($status, $message, $data = []) {
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id <= 0) {
    sendResponse('error', 'Invalid user ID');
}

// Fetch checkout records for the user
$query = "SELECT * FROM checkout WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $checkouts = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($checkouts as &$checkout) {
        $checkout_id = $checkout['checkout_id'];

        // Fetch each product in checkout_details as separate entries
        $detailQuery = "
            SELECT cd.*, c.product_quantity, c.product_price 
            FROM checkout_details cd
            LEFT JOIN carts c ON cd.product_id = c.product_id 
            WHERE cd.checkout_id = ? AND c.user_id = ?
        ";
        $detailStmt = $conn->prepare($detailQuery);
        $detailStmt->bind_param("ii", $checkout_id, $user_id);
        $detailStmt->execute();
        $detailResult = $detailStmt->get_result();

        // Store each product detail as a separate entry in the details array
        $checkout['details'] = [];
        while ($detail = $detailResult->fetch_assoc()) {
            $checkout['details'][] = $detail;
        }
    }

    sendResponse('success', 'Checkouts fetched successfully', $checkouts);
} else {
    sendResponse('error', 'Error executing query');
}
?>
