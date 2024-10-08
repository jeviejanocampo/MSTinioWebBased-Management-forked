<?php
header('Content-Type: application/json');

include_once 'dbcon.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['user_pin'])) {
    $user_pin = $data['user_pin'];

    $stmt = $conn->prepare("SELECT * FROM m_delivery_personnel WHERE dp_access_pin = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database statement preparation failed']);
        exit;
    }

    $stmt->bind_param("s", $user_pin);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'PIN verified successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect PIN']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No PIN provided']);
}
?>
