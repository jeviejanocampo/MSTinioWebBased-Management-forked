<?php
header('Content-Type: application/json');
include 'dbcon.php'; // Database connection

// Get user_id from query parameter
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if ($user_id) {
    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT id, user_id, d_full_name, d_contact_number, d_address, d_postal_code, u_longitude, u_latitude, d_status 
                             FROM delivery_address 
                             WHERE user_id = ? AND d_status = 'DELIVERY ADDRESS SAVED'");
    $stmt->bind_param("i", $user_id); // Assuming user_id is an integer

    // Execute the statement
    $stmt->execute();
    
    // Get the result
    $result = $stmt->get_result();
    $addresses = [];

    // Fetch all delivery addresses
    while ($row = $result->fetch_assoc()) {
        $addresses[] = $row;
    }

    // Return the addresses as JSON
    echo json_encode($addresses);
} else {
    // Return an error message if user_id is not provided
    echo json_encode(['error' => 'User ID is required']);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
