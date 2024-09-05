<?php
header('Content-Type: application/json');

// Include database connection
include 'dbcon.php';

// Function to send JSON response
function sendResponse($status, $message, $data = []) {
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
}

// Check the request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Invalid request method.');
    exit();
}

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'] ?? null;
$product_id = $data['product_id'] ?? null;
$product_price = $data['product_price'] ?? null;
$product_quantity = $data['product_quantity'] ?? 1; // Default quantity to 1

if (!$user_id || !$product_id || !$product_price) {
    sendResponse('error', 'Required fields are missing.');
    exit();
}

// Check if the product already exists in the cart
$query = "
    SELECT * FROM carts 
    WHERE user_id = ? AND product_id = ? AND product_price = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param('iid', $user_id, $product_id, $product_price);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Product already exists, update the quantity
    $existing_item = $result->fetch_assoc();
    $new_quantity = $existing_item['product_quantity'] + $product_quantity;

    $update_query = "
        UPDATE carts 
        SET product_quantity = ?, cart_status = 'to be checked out'
        WHERE user_id = ? AND product_id = ? AND product_price = ?
    ";

    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('iidi', $new_quantity, $user_id, $product_id, $product_price);

    if ($update_stmt->execute()) {
        sendResponse('success', 'Product quantity updated successfully.');
    } else {
        sendResponse('error', 'Failed to update product quantity.');
    }
    
    $update_stmt->close();
} else {
    // Product does not exist, insert a new entry
    $insert_query = "
        INSERT INTO carts (user_id, product_id, product_price, product_quantity, cart_status) 
        VALUES (?, ?, ?, ?, 'to be checked out')
    ";

    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param('iidi', $user_id, $product_id, $product_price, $product_quantity);

    if ($insert_stmt->execute()) {
        sendResponse('success', 'Product added to cart successfully.');
    } else {
        sendResponse('error', 'Failed to add product to cart.');
    }

    $insert_stmt->close();
}

// Close the database connection
$stmt->close();
mysqli_close($conn);
?>
