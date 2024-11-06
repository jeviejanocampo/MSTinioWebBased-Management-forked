<?php
header('Content-Type: application/json');
include 'dbcon.php';

$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input['product_id']) && is_numeric($input['product_id'])) {
    $product_id = (int) $input['product_id'];

    try {
        $stmt = $conn->prepare("SELECT product_stocks FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode(['product_stocks' => (int) $row['product_stocks']]);
        } else {
            echo json_encode(['error' => 'Product not found.']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    } finally {
        $stmt->close();
    }
} else {
    echo json_encode(['error' => 'Invalid request.']);
}

$conn->close();
?>
