<?php
header('Content-Type: application/json');

// Include database connection
include 'dbcon.php';

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Check if $data is not null and contains required fields
if ($data === null || !isset($data['user_id']) || !isset($data['items'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
    exit();
}

$user_id = $data['user_id'];
$items = $data['items']; // array of items with cart_id

// Initialize response array
$response = ['status' => 'error', 'message' => ''];

// Begin transaction
$conn->begin_transaction();

try {
    // Check if the checkout status is 'Completed' for the given user
    $checkStatusQuery = "
        SELECT cart_id 
        FROM checkout 
        WHERE user_id = ? 
        AND checkout_status = 'Completed'
    ";
    $stmt = $conn->prepare($checkStatusQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $completedCartIds = [];
    while ($row = $result->fetch_assoc()) {
        $completedCartIds[] = $row['cart_id'];
    }

    if (empty($completedCartIds)) {
        throw new Exception('No completed checkouts found for this user.');
    }

    // Check if items to be deleted are in the completed cart IDs
    $cartIdsToDelete = array_filter($items, function($item) use ($completedCartIds) {
        return in_array($item['cart_id'], $completedCartIds);
    });

    if (empty($cartIdsToDelete)) {
        throw new Exception('No valid cart items to delete.');
    }

    // Prepare query to delete items from carts
    $placeholders = implode(',', array_fill(0, count($cartIdsToDelete), '?'));
    $deleteQuery = "
        DELETE FROM carts 
        WHERE user_id = ? 
        AND cart_id IN ($placeholders)
    ";
    $stmt = $conn->prepare($deleteQuery);

    // Bind parameters dynamically
    $types = str_repeat('i', count($cartIdsToDelete)) . 'i';
    $params = array_merge([$types], $cartIdsToDelete, [$user_id]);
    $stmt->bind_param(...$params);

    if (!$stmt->execute()) {
        throw new Exception('Failed to delete cart items.');
    }

    // Commit transaction
    $conn->commit();
    $response['status'] = 'success';
    $response['message'] = 'Cart items successfully deleted.';
} catch (Exception $e) {
    // Rollback transaction
    $conn->rollback();
    $response['message'] = $e->getMessage();
}

// Close connection
$conn->close();

// Send response
echo json_encode($response);
?>
