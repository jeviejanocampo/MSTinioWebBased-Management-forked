<?php
header('Content-Type: application/json');

include 'dbcon.php'; // Assuming dbcon.php contains your database connection logic

// Get the input data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Check if user_id is provided
if (isset($data['user_id'])) {
    $user_id = $data['user_id'];

    // Prepare SQL query to fetch order_id, order_status, checkout_id, and order_date from orders table based on user_id
    $sql = "SELECT order_id, order_status, checkout_id, order_date FROM orders WHERE user_id = ?";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind the user_id parameter to the query
        $stmt->bind_param('i', $user_id);

        // Execute the query
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Initialize an array to store the order details
        $orderDetails = array();

        // Fetch the order details from the result
        while ($row = $result->fetch_assoc()) {
            $order_id = $row['order_id'];
            $order_status = $row['order_status'];
            $checkout_id = $row['checkout_id'];
            $order_date = $row['order_date']; // Fetch order_date
            $total_amount = 0;
            $items_count = 0;

            // Fetch total_amount from the checkout table based on checkout_id
            $sqlCheckout = "SELECT total_amount FROM checkout WHERE checkout_id = ? AND user_id = ?";
            if ($stmtCheckout = $conn->prepare($sqlCheckout)) {
                $stmtCheckout->bind_param('ii', $checkout_id, $user_id);
                $stmtCheckout->execute();
                $checkoutResult = $stmtCheckout->get_result();
                if ($checkoutRow = $checkoutResult->fetch_assoc()) {
                    $total_amount = $checkoutRow['total_amount'];
                }
                $stmtCheckout->close();
            }

            // Fetch the total product_quantity, product_name, and product details from products table based on product_id
            $sqlCheckoutDetails = "
                SELECT cd.product_id, cd.product_quantity, p.product_price, p.product_image, p.product_name 
                FROM checkout_details cd
                JOIN products p ON cd.product_id = p.product_id 
                WHERE cd.checkout_id = ?";
            if ($stmtDetails = $conn->prepare($sqlCheckoutDetails)) {
                $stmtDetails->bind_param('i', $checkout_id);
                $stmtDetails->execute();
                $detailsResult = $stmtDetails->get_result();

                // Initialize an array to store the product details
                $products = array();
                while ($detailsRow = $detailsResult->fetch_assoc()) {
                    $productImage = $detailsRow['product_image'];
                    // Construct the full image URL
                    $imageUrl = "http://192.168.1.32/capstone-template/product-images/" . $productImage;

                    // Fetch product price from the products table based on product_id
                    $product_price = $detailsRow['product_price']; // Now fetching product_price directly from products

                    $products[] = array(
                        'product_id' => $detailsRow['product_id'],
                        'product_name' => $detailsRow['product_name'], // Include product_name
                        'product_quantity' => $detailsRow['product_quantity'],
                        'product_price' => $product_price, // Include the product price from products table
                        'image_url' => $imageUrl // Include the image URL in the product details
                    );
                }
                $stmtDetails->close();

                // Count the total quantity of products
                $items_count = array_sum(array_column($products, 'product_quantity'));
            }

            // Append details to the orderDetails array
            $orderDetails[] = array(
                'order_id' => $order_id,
                'order_status' => $order_status,
                'order_date' => $order_date, // Include order_date
                'total_amount' => $total_amount,
                'items_count' => $items_count,
                'products' => $products // Include the product details
            );
        }

        // Return the order details as JSON
        echo json_encode($orderDetails);

        // Close the statement
        $stmt->close();
    } else {
        // Return an error if the statement couldn't be prepared
        echo json_encode(['error' => 'Unable to prepare statement']);
    }

    // Close the database connection
    $conn->close();
} else {
    // Return an error if user_id is not provided
    echo json_encode(['error' => 'User ID not provided']);
}
?>
