<?php
header('Content-Type: application/json');

// Include database connection file
include_once 'dbcon.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get the raw POST data
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate that all required fields are provided
    if (isset($data['cart_id'], $data['product_quantity'])) {

        // Assign variables
        $cart_id = $data['cart_id'];
        $product_quantity = intval($data['product_quantity']); // Ensure quantity is an integer

        // Update the cart item quantity in the database
        $sql = "UPDATE carts SET product_quantity = ? WHERE cart_id = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ii", $product_quantity, $cart_id);

            if ($stmt->execute()) {
                // Check if any rows were affected
                if ($stmt->affected_rows > 0) {
                    $response = [
                        'status' => 'success',
                        'message' => 'Product quantity updated successfully.'
                    ];
                } else {
                    $response = [
                        'status' => 'error',
                        'message' => 'No rows were updated. The cart item may not exist.'
                    ];
                }
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Failed to execute query.'
                ];
            }
            $stmt->close();
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Failed to prepare the SQL statement.'
            ];
        }

    } else {
        $response = [
            'status' => 'error',
            'message' => 'Required fields missing: cart_id, product_quantity.'
        ];
    }

} else {
    $response = [
        'status' => 'error',
        'message' => 'Invalid request method. Please use POST.'
    ];
}

// Close the database connection
$conn->close();

// Send the response back as JSON
echo json_encode($response);
?>
