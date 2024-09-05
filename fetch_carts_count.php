<?php
// Include your database connection file
include 'dbcon.php'; // Ensure this file contains your database connection code

// Get the user_id from the query parameters
$user_id = $_GET['user_id'] ?? null;

// Check if user_id is provided
if ($user_id) {
    // Prepare the SQL query to fetch cart items for the given user_id
    $sql = "SELECT c.cart_id, p.product_name, c.product_quantity, c.product_price
            FROM carts c
            JOIN products p ON c.product_id = p.product_id
            WHERE c.user_id = ?";
    
    // Prepare and execute the query
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Fetch the data
        $cartItems = [];
        while ($row = $result->fetch_assoc()) {
            $cartItems[] = $row;
        }

        // Set response header to JSON
        header('Content-Type: application/json');
        
        // Return JSON-encoded data
        echo json_encode($cartItems);
        
        // Close the statement
        $stmt->close();
    } else {
        // Error preparing statement
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Failed to prepare SQL query']);
    }
} else {
    // Missing user_id
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'User ID is required']);
}

// Close the database connection
$conn->close();
