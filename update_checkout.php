<?php
include 'dbcon.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'];
$items = $data['items'];
$total_amount = $data['total_amount'];

// Assuming you have a checkout_details table where the checkout items are stored
try {
    $conn->autocommit(FALSE); // Start a transaction

    foreach ($items as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['product_quantity'];
        $price = $item['product_price'];

        // Update the quantities in the checkout_details table
        $query = "UPDATE checkout_details 
                  SET product_quantity = ?, product_price = ?
                  WHERE user_id = ? AND product_id = ? AND checkout_status = 'Pending'";

        $stmt = $conn->prepare($query);
        $stmt->bind_param('idii', $quantity, $price, $user_id, $product_id);
        $stmt->execute();
    }

    // Update the total amount in the checkout table
    $query = "UPDATE checkout SET total_amount = ? WHERE user_id = ? AND checkout_status = 'Pending'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('di', $total_amount, $user_id);
    $stmt->execute();

    $conn->commit(); // Commit the transaction

    echo json_encode(['status' => 'success', 'message' => 'Checkout updated successfully.']);
} catch (Exception $e) {
    $conn->rollback(); // Rollback the transaction on error
    echo json_encode(['status' => 'error', 'message' => 'Failed to update checkout.', 'error' => $e->getMessage()]);
}

$conn->close();
?>
