<?php
header('Content-Type: application/json');

// Include database connection
include 'dbcon.php';

// Read the raw POST data from the request body
$data = json_decode(file_get_contents('php://input'), true);

// Check if all necessary fields are present in the incoming request
if (isset($data['user_id']) && isset($data['d_full_name']) && isset($data['d_contact_number']) && isset($data['d_address']) && isset($data['d_postal_code']) && isset($data['u_latitude']) && isset($data['u_longitude'])) {
    
    // Sanitize the input data
    $user_id = mysqli_real_escape_string($conn, $data['user_id']);
    $full_name = mysqli_real_escape_string($conn, $data['d_full_name']);
    $contact_number = mysqli_real_escape_string($conn, $data['d_contact_number']);
    $address = mysqli_real_escape_string($conn, $data['d_address']);
    $postal_code = mysqli_real_escape_string($conn, $data['d_postal_code']);
    $latitude = mysqli_real_escape_string($conn, $data['u_latitude']);
    $longitude = mysqli_real_escape_string($conn, $data['u_longitude']);

    // Prepare the SQL query to insert the data into the delivery_address table
    $sql = "INSERT INTO delivery_address (user_id, d_full_name, d_contact_number, d_address, d_postal_code, u_longitude, u_latitude) 
            VALUES ('$user_id', '$full_name', '$contact_number', '$address', '$postal_code', '$longitude', '$latitude')";

    // Execute the query and check if the insertion was successful
    if (mysqli_query($conn, $sql)) {
        // Success response
        echo json_encode([
            'status' => 'success',
            'message' => 'Delivery address inserted successfully'
        ]);
    } else {
        // Error response if insertion fails
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to insert delivery address',
            'error' => mysqli_error($conn)
        ]);
    }
} else {
    // Error response if required fields are missing
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid input data. Please provide all required fields.'
    ]);
}

// Close the database connection
mysqli_close($conn);
?>
