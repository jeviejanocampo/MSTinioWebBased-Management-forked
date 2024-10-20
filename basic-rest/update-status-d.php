<?php
header('Content-Type: application/json');
include 'dbcon.php'; 

function sendResponse($status, $data = null, $message = null) {
    echo json_encode([
        'status' => $status,
        'data' => $data,
        'message' => $message
    ]);
    exit();
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    sendResponse('error', null, 'Invalid JSON input');
}

if (!isset($data['id']) || !isset($data['user_id']) || !isset($data['status'])) {
    sendResponse('error', null, 'ID, User ID, and status are required.');
}

$id = intval($data['id']);
$user_id = intval($data['user_id']);
$status = $conn->real_escape_string($data['status']); 

$conn->begin_transaction();

try {
    if ($status === 'DELIVERY ADDRESS SAVED') {
        $querySave = "UPDATE delivery_address SET d_status = ? WHERE id = ? AND user_id = ?";
        $stmtSave = $conn->prepare($querySave);
        $stmtSave->bind_param("sii", $status, $id, $user_id);

        if (!$stmtSave->execute()) {
            throw new Exception('Error updating the saved address: ' . $stmtSave->error);
        }

        $queryUnsave = "UPDATE delivery_address SET d_status = 'UNSAVED' WHERE id != ? AND user_id = ?";
        $stmtUnsave = $conn->prepare($queryUnsave);
        $stmtUnsave->bind_param("ii", $id, $user_id);

        if (!$stmtUnsave->execute()) {
            throw new Exception('Error updating the unsaved addresses: ' . $stmtUnsave->error);
        }

    } else if ($status === 'UNSAVED') {
        $queryUnsaveAll = "UPDATE delivery_address SET d_status = 'UNSAVED' WHERE user_id = ?";
        $stmtUnsaveAll = $conn->prepare($queryUnsaveAll);
        $stmtUnsaveAll->bind_param("i", $user_id);

        if (!$stmtUnsaveAll->execute()) {
            throw new Exception('Error updating all addresses to unsaved: ' . $stmtUnsaveAll->error);
        }
    }

    $conn->commit();

    $selectQuery = "SELECT id, d_status FROM delivery_address WHERE user_id = ?";
    $selectStmt = $conn->prepare($selectQuery);
    $selectStmt->bind_param("i", $user_id);
    $selectStmt->execute();
    $result = $selectStmt->get_result();

    $addresses = [];
    while ($row = $result->fetch_assoc()) {
        $addresses[] = $row;
    }

    sendResponse('success', $addresses, 'Delivery address status updated successfully.');

} catch (Exception $e) {
    $conn->rollback();
    sendResponse('error', null, $e->getMessage());
}

$stmtSave->close();
if (isset($stmtUnsave)) {
    $stmtUnsave->close();
}
if (isset($stmtUnsaveAll)) {
    $stmtUnsaveAll->close();
}
$conn->close();
?>
