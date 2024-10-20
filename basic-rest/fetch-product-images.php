<?php
header('Content-Type: application/json');

// Include database connection
include 'dbcon.php';  // Ensure this file establishes a $conn connection

// Initialize an empty array to hold the results
$response = [];

// Define the base URL for the product images
$base_url = 'http://192.168.1.32/capstone-template/product-images/';

// Fetch the product images from the database
try {
    // Prepare and execute the query
    $query = "SELECT product_name, product_image FROM products"; // Adjust the query as needed
    $result = $conn->query($query); // Use the mysqli connection to execute the query

    // Check if the query was successful
    if ($result) {
        // Fetch all results as an associative array
        while ($product = $result->fetch_assoc()) {
            $response[] = [
                'product_name' => $product['product_name'],
                'product_image' => $base_url . $product['product_image'] // Concatenate the base URL with the image filename
            ];
        }

        // Free the result set
        $result->free();
    } else {
        // Handle query error
        echo json_encode(['error' => 'Query error: ' . $conn->error]);
        exit();
    }
} catch (Exception $e) {
    // Handle any exceptions
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}

// Return the response as JSON
echo json_encode($response);

// Close the database connection (optional, as it will close when the script ends)
$conn->close();
?>
