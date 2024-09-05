<?php
header('Content-Type: application/json');

// Include database connection
include 'dbcon.php';

// Function to send JSON response
function sendResponse($status, $message, $data = []) {
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'products' => $data
    ]);
}

// Check the request method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse('error', 'Invalid request method.');
    exit();
}

// Query to fetch products with details
$query = "
    SELECT p.product_id, p.product_name, p.product_price, p.product_description, d.category_name 
    FROM Products p
    INNER JOIN product_details d ON p.product_id = d.product_details_id
";

$result = mysqli_query($conn, $query);

// Check if the query was successful
if (!$result) {
    sendResponse('error', 'Database query failed.');
    exit();
}

// Fetch the data
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

// Send a successful response
sendResponse('success', 'Products fetched successfully.', $products);

// Close the database connection
mysqli_close($conn);
?>
