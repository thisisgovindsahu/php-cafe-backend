<?php
// Include the database connection file
include "../../../db-conn/connect.php";
include "../../middlewares/isAdmin.php";

// Set the content type to JSON
header('Content-Type: application/json');

// Check if the request method is DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    // Check if the ID is passed in the query string
    if (isset($_GET['orderid'])) {
        $id = $_GET['orderid'];

        try {
            // Prepare the DELETE query
            $deleteQuery = $conn->prepare("DELETE FROM orders WHERE id = ?");
            $delete = $deleteQuery->execute([$id]);

            // Check if the row was deleted
            if ($delete) {
                echo json_encode([
                    "success" => true,
                    "message" => "Order deleted successfully."
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Order not found or already deleted."
                ]);
            }
        } catch (PDOException $e) {
            // Handle any database errors
            echo json_encode([
                "success" => false,
                "message" => "Failed to delete order: " . $e->getMessage()
            ]);
        }
    } else {
        // ID is missing in the request
        echo json_encode([
            "success" => false,
            "message" => "Order ID is required."
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
