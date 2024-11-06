<?php
header('Content-Type: application/json');

include 'dbcon.php'; // Ensure this file contains your database connection

// Get the raw POST data
$data = json_decode(file_get_contents("php://input"));

// Validate the input
if (isset($data->order_id)) {
    $orderId = $data->order_id;

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT delivery_personnel_id, assigned_status, created_at FROM assigned_delivery_orders WHERE orders_id = ?");
    $stmt->bind_param("i", $orderId); // Assuming order_id is an integer

    // Execute the statement
    if ($stmt->execute()) {
        // Get the result
        $result = $stmt->get_result();
        
        // Check if any rows were returned
        if ($result->num_rows > 0) {
            // Fetch the data
            $deliveryDetails = $result->fetch_assoc();
            echo json_encode($deliveryDetails);
        } else {
            // No records found
            echo json_encode(["message" => "No delivery details found for the provided order ID."]);
        }
    } else {
        // Query execution error
        echo json_encode(["error" => "Query execution failed: " . $stmt->error]);
    }

    // Close the statement
    $stmt->close();
} else {
    // Invalid input
    echo json_encode(["error" => "Invalid input. Order ID is required."]);
}

// Close the database connection
$conn->close();
?>
