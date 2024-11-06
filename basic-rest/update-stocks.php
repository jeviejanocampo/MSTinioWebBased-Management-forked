<?php
// Include the database connection
require_once 'dbcon.php'; // Ensure the path is correct

function updateProductStocks($conn) {
    // Fetch all COMPLETED checkouts with their product details
    $query = "
        SELECT 
            cd.product_id, cd.product_quantity, p.product_stocks, cd.checkout_id
        FROM 
            checkout_details cd
        JOIN 
            checkout c ON cd.checkout_id = c.checkout_id
        JOIN 
            products p ON cd.product_id = p.product_id
        WHERE 
            c.checkout_status = 'COMPLETED'
    ";

    $result = $conn->query($query);

    if ($result === false) {
        echo "Error fetching data: " . $conn->error . "\n";
        return;
    }

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $product_id = $row['product_id'];
            $product_quantity = (int) $row['product_quantity'];
            $current_stock = (int) $row['product_stocks'];
            $checkout_id = $row['checkout_id'];

            echo "Processing Product ID: $product_id | Quantity: $product_quantity | Current Stock: $current_stock\n";

            // Calculate the new stock level
            $new_stock = $current_stock - $product_quantity;

            if ($new_stock < 0) {
                echo "Error: Insufficient stock for Product ID $product_id.\n";
                continue; // Skip if stock becomes negative
            }

            // Update the product stock in the products table
            $updateQuery = "UPDATE products SET product_stocks = ? WHERE product_id = ?";
            $stmt = $conn->prepare($updateQuery);
            if (!$stmt) {
                echo "Prepare failed: " . $conn->error . "\n";
                continue;
            }

            $stmt->bind_param('ii', $new_stock, $product_id);

            if ($stmt->execute()) {
                echo "Product ID $product_id stock updated to $new_stock.\n";

                // Optional: Mark checkout as processed to avoid re-updating
                $markProcessedQuery = "
                    UPDATE checkout SET checkout_status = 'PROCESSED' WHERE checkout_id = ?
                ";
                $markStmt = $conn->prepare($markProcessedQuery);
                $markStmt->bind_param('i', $checkout_id);
                $markStmt->execute();
                $markStmt->close();
            } else {
                echo "Failed to update product stock for Product ID $product_id: " . $stmt->error . "\n";
            }

            $stmt->close();
        }
    } else {
        echo "No COMPLETED checkouts found.\n";
    }
}

// Polling mechanism (every 5 seconds)
while (true) {
    updateProductStocks($conn);
    sleep(5); // Wait for 5 seconds before the next poll
}

$conn->close();
?>
