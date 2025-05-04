<?php
// Include the database connection file
include "../../../db-conn/connect.php";

// Set the content type to JSON
header('Content-Type: application/json');

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Fetch all products from the database
        $query = $conn->prepare("SELECT * FROM products ORDER BY time DESC");
        $query->execute();

        // Check if any products are found
        if ($query->rowCount() > 0) {
            $products = $query->fetchAll(PDO::FETCH_ASSOC);

            // Send the products as a JSON response
            echo json_encode([
                "success" => true,
                "data" => $products
            ]);
        } else {
            // No products found
            echo json_encode([
                "success" => false,
                "message" => "No products found."
            ]);
        }
    } catch (PDOException $e) {
        // Send an error response
        echo json_encode([
            "success" => false,
            "message" => "Failed to fetch products: " . $e->getMessage()
        ]);
    }
} else {
    // Send a response if the request method is not GET
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Use GET."
    ]);
}
?>
