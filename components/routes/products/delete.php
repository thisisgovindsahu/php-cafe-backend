<?php
// Include the database connection file
include "../../../db-conn/connect.php";
include "../../middlewares/isAdmin.php";

// Set the content type to JSON
header('Content-Type: application/json');

// Check if the request method is DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Check if the ID is passed in the query string
    if (isset($_GET['productid'])) {
        $id = $_GET['productid'];

        try {
            // Prepare the DELETE query
            $deleteQuery = $conn->prepare("DELETE FROM products WHERE id = ?");
            $deleteQuery->execute([$id]);

            // Check if the row was deleted
            if ($deleteQuery->rowCount() > 0) {
                echo json_encode([
                    "success" => true,
                    "message" => "Item deleted successfully."
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Item not found or already deleted."
                ]);
            }
        } catch (PDOException $e) {
            // Handle any database errors
            echo json_encode([
                "success" => false,
                "message" => "Failed to delete item: " . $e->getMessage()
            ]);
        }
    } else {
        // ID is missing in the request
        echo json_encode([
            "success" => false,
            "message" => "Product ID is required."
        ]);
    }
} else {
    // Send a response for invalid request method
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Use DELETE."
    ]);
}
?>
