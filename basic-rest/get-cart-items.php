<?php
header('Content-Type: application/json');

// Include database connection file
include_once 'dbcon.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate that the user_id is provided
    if (isset($data['user_id'])) {
        $user_id = intval($data['user_id']); // Ensure user_id is an integer

        // Prepare SQL statement to fetch cart items
        $sql = "SELECT cart_id, product_id, product_quantity FROM carts WHERE user_id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Fetch all cart items
            $cartItems = [];
            while ($row = $result->fetch_assoc()) {
                $cartItems[] = $row;
            }

            // Respond with the cart items
            echo json_encode(['status' => 'success', 'cartItems' => $cartItems]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare the SQL statement.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Required field missing: user_id.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Please use POST.']);
}

// Close the database connection
$conn->close();
?>
