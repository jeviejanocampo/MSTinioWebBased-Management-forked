<?php
header('Content-Type: application/json');

// Include database connection
include 'dbcon.php';

// Define the base URL for the images
$image_base_url = 'http://192.168.1.32/capstone-template/product-images/'; // Base URL to your image folder

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

// Query to fetch products with details, including the product_image
$query = "
    SELECT p.product_id, p.product_name, p.product_price, p.product_description, d.category_name, p.product_image 
    FROM Products p
    INNER JOIN product_details d ON p.product_details_id = d.product_details_id
";

// Execute the query
$result = mysqli_query($conn, $query);

// Check if the query was successful
if (!$result) {
    sendResponse('error', 'Database query failed.');
    exit();
}

// Fetch the data
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Prepend the base URL to the product image filename
    $row['product_image'] = $image_base_url . $row['product_image']; // Append image path to the URL

    // Add the product to the list
    $products[] = $row;
}

// Send a successful response
sendResponse('success', 'Products fetched successfully.', $products);

// Close the database connection
mysqli_close($conn);
?>
