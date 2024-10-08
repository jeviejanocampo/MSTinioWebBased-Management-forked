<?php
header('Content-Type: application/json');
include 'dbcon.php';  // Ensure this file contains your database connection logic

function sendResponse($status, $message) {
    echo json_encode([
        'status' => $status,
        'message' => $message
    ]);
    exit();
}

// Get the raw POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate the input data
if (!isset($data['user_id']) || !is_int($data['user_id'])) {
    sendResponse('error', 'Invalid or missing user ID');
}

if (!isset($data['d_full_name']) || empty($data['d_full_name'])) {
    sendResponse('error', 'Full name is required');
}

if (!isset($data['d_contact_number']) || empty($data['d_contact_number'])) {
    sendResponse('error', 'Contact number is required');
}

if (!isset($data['d_address']) || empty($data['d_address'])) {
    sendResponse('error', 'Address is required');
}

if (!isset($data['d_postal_code']) || empty($data['d_postal_code'])) {
    sendResponse('error', 'Postal code is required');
}

if (!isset($data['d_status']) || empty($data['d_status'])) {
    sendResponse('error', 'Status is required');
}

// Sanitize and assign input data to variables
$user_id = intval($data['user_id']);
$full_name = $conn->real_escape_string($data['d_full_name']);
$contact_number = $conn->real_escape_string($data['d_contact_number']);
$address = $conn->real_escape_string($data['d_address']);
$postal_code = $conn->real_escape_string($data['d_postal_code']);
$status = $conn->real_escape_string($data['d_status']);
$access_token = $conn->real_escape_string($data['d_access_token']); // Get the access token

// Check if a delivery address already exists for this user
$query_check = "SELECT * FROM delivery_address WHERE user_id = ?";
$stmt_check = $conn->prepare($query_check);
$stmt_check->bind_param('i', $user_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // Update the existing delivery address
    $query_update = "UPDATE delivery_address SET d_full_name = ?, d_contact_number = ?, d_address = ?, d_postal_code = ?, d_status = ?, d_access_token = ? WHERE user_id = ?";
    $stmt_update = $conn->prepare($query_update);
    $stmt_update->bind_param('ssssssi', $full_name, $contact_number, $address, $postal_code, $status, $access_token, $user_id); // Bind the access token

    if ($stmt_update->execute()) {
        sendResponse('success', 'Delivery address updated successfully');
    } else {
        sendResponse('error', 'Failed to update delivery address');
    }
} else {
    // Insert a new delivery address
    $query_insert = "INSERT INTO delivery_address (user_id, d_full_name, d_contact_number, d_address, d_postal_code, d_status, d_access_token) VALUES (?, ?, ?, ?, ?, ?, ?)"; // Add d_access_token to the insert query
    $stmt_insert = $conn->prepare($query_insert);
    $stmt_insert->bind_param('issssss', $user_id, $full_name, $contact_number, $address, $postal_code, $status, $access_token); // Bind the access token

    if ($stmt_insert->execute()) {
        sendResponse('success', 'Delivery address saved successfully');
    } else {
        sendResponse('error', 'Failed to save delivery address');
    }
}

// Close the database connection
$stmt_check->close();   
$conn->close();
?>
