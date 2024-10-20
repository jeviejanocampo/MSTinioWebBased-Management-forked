<?php
header('Content-Type: application/json');
include 'dbcon.php';

function sendResponse($status, $data = null, $message = null) {
    echo json_encode([
        'status' => $status,
        'data' => $data,
        'message' => $message
    ]);
    exit();
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['user_id']) || empty($data['user_id'])) {
    sendResponse('error', null, 'Invalid User ID');
}

$user_id = intval($data['user_id']);

$query = "SELECT id, user_id, d_full_name, d_contact_number, d_address, d_postal_code, u_longitude, u_latitude, d_status 
          FROM delivery_address
          WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $addresses = $result->fetch_all(MYSQLI_ASSOC);

    if (count($addresses) > 0) {
        sendResponse('success', $addresses);
    } else {
        sendResponse('error', null, 'No delivery addresses found for this user');
    }
} else {
    sendResponse('error', null, 'Error fetching delivery addresses');
}

$stmt->close();
$conn->close();
?>
