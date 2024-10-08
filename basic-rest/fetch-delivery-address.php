<?php
header('Content-Type: application/json');
include 'dbcon.php'; // Ensure your database connection file is included

function sendResponse($status, $data = null, $message = null) {
    echo json_encode([
        'status' => $status,
        'data' => $data,
        'message' => $message
    ]);
    exit();
}

// Get the raw input data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Check if user_id is set and is valid
if (!isset($data['user_id']) || empty($data['user_id'])) {
    sendResponse('error', null, 'Invalid User ID');
}

$user_id = intval($data['user_id']); // Convert user_id to integer

// Prepare the SQL statement to fetch delivery addresses
$query = "SELECT user_id, d_access_token, d_full_name, d_contact_number, d_address, d_postal_code, d_status 
          FROM delivery_address
          WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $addresses = $result->fetch_all(MYSQLI_ASSOC); // Fetch all delivery addresses

    if (count($addresses) > 0) {
        sendResponse('success', $addresses); // Send the delivery addresses as data
    } else {
        sendResponse('error', null, 'No delivery addresses found for this user');
    }
} else {
    sendResponse('error', null, 'Error fetching delivery addresses');
}

$stmt->close();
$conn->close();
?>
