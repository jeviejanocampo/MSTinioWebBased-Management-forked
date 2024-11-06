<?php
header('Content-Type: application/json');
include 'dbcon.php';

$response = array();

// Get the JSON input and decode it
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Check if user_id is provided
if (isset($data['user_id'])) {
    $user_id = $data['user_id'];

    // Prepare the SQL query
    $query = "SELECT d_full_name, d_contact_number, d_address, d_postal_code 
              FROM delivery_address 
              WHERE user_id = ?";
              
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the user has a delivery address
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $response['success'] = true;
            $response['d_full_name'] = $row['d_full_name'];
            $response['d_contact_number'] = $row['d_contact_number'];
            $response['d_address'] = $row['d_address'];
            $response['d_postal_code'] = $row['d_postal_code'];
        } else {
            $response['success'] = false;
            $response['message'] = "No delivery address found for this user.";
        }

        $stmt->close();
    } else {
        $response['success'] = false;
        $response['message'] = "Failed to prepare statement.";
    }
} else {
    $response['success'] = false;
    $response['message'] = "User ID not provided.";
}

// Close the database connection
$conn->close();

// Output the JSON response
echo json_encode($response);
?>
