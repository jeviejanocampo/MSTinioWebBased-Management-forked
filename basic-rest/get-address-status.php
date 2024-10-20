<?php
header('Content-Type: application/json');
include 'dbcon.php'; // Database connection

function sendResponse($status, $data = null, $message = null) {
    echo json_encode([
        'status' => $status,
        'data' => $data,
        'message' => $message
    ]);
    exit();
}

// Check if ID is provided and sanitize it
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$id) {
    sendResponse('error', null, 'ID is required.');
}

// Prepare SQL statement to fetch delivery address status
$query = "SELECT d_status FROM delivery_address WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    sendResponse('error', null, 'Failed to prepare the SQL statement.');
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        sendResponse('success', ['d_status' => $row['d_status']], 'Delivery address status retrieved successfully.');
    } else {
        sendResponse('error', null, 'Address not found.');
    }
} else {
    sendResponse('error', null, 'Error fetching delivery address status.');
}

$stmt->close();
$conn->close();
