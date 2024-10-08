<?php
// delete_checkout.php
header('Content-Type: application/json');
include 'dbcon.php'; // Make sure this file contains the necessary database connection setup

$response = array('status' => 'success', 'message' => 'Record deleted successfully');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $checkout_id = $data['checkout_id'] ?? null;

    if ($checkout_id) {
        // Start a transaction
        mysqli_begin_transaction($conn);

        try {
            // Delete from checkout_details table
            $query1 = "DELETE FROM checkout_details WHERE checkout_id = ?";
            $stmt1 = mysqli_prepare($conn, $query1);
            mysqli_stmt_bind_param($stmt1, 'i', $checkout_id);
            mysqli_stmt_execute($stmt1);

            // Delete from checkout table
            $query2 = "DELETE FROM checkout WHERE checkout_id = ?";
            $stmt2 = mysqli_prepare($conn, $query2);
            mysqli_stmt_bind_param($stmt2, 'i', $checkout_id);
            mysqli_stmt_execute($stmt2);

            // Commit the transaction
            mysqli_commit($conn);

            // Check if deletion was successful
            if (mysqli_stmt_affected_rows($stmt1) >= 0 && mysqli_stmt_affected_rows($stmt2) >= 0) {
                echo json_encode($response);
            } else {
                throw new Exception('Failed to delete record');
            }
        } catch (Exception $e) {
            // Rollback the transaction on error
            mysqli_rollback($conn);

            $response['status'] = 'error';
            $response['message'] = $e->getMessage();
            echo json_encode($response);
        }

        // Close statements
        mysqli_stmt_close($stmt1);
        mysqli_stmt_close($stmt2);
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Invalid checkout_id';
        echo json_encode($response);
    }
} else {
    http_response_code(405); // Method Not Allowed
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
}

// Close database connection
mysqli_close($conn);
?>
