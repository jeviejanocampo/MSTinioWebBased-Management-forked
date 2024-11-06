<?php
header('Content-Type: application/json');
include 'dbcon.php'; // Ensure you include your database connection file

// Retrieve the JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate and get the order_id
if (isset($data['order_id'])) {
    $order_id = $data['order_id'];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT payment_id, order_id, user_id, payment_method, amount_paid, payment_date, payment_status, completed_at, created_at FROM payments WHERE order_id = ?");
    $stmt->bind_param("s", $order_id); // Assuming order_id is a string; change "s" to "i" if it's an integer

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the payment details
    if ($result->num_rows > 0) {
        $paymentDetails = $result->fetch_assoc();
        // Return a success response with payment details
        echo json_encode(['success' => true, 'data' => $paymentDetails]);
    } else {
        // No payment details found for the given order_id
        echo json_encode(['success' => false, 'message' => 'No payment details found for this order.']);
    }
} else {
    // Invalid request, missing order_id
    echo json_encode(['success' => false, 'message' => 'Invalid request, order_id is required.']);
}

// Close the database connection
$stmt->close();
$conn->close();
?>
