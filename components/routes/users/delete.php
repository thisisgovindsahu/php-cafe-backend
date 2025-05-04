<?php
// Include the database connection file
include "../../../db-conn/connect.php";
include "../../middlewares/isAdmin.php";

// Set the content type to JSON
header('Content-Type: application/json');

// Check if the request method is DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    // Check if the ID is passed in the query string
    if (!empty($_GET['id'])) {
        $id = $_GET['id'];

        try {
            // Prepare the DELETE query
            $deleteQuery = $conn->prepare("DELETE FROM users WHERE id = ?");
            $deleteQuery->execute([$id]);

            // Check if the row was deleted
            if ($deleteQuery->rowCount() > 0) {
                echo json_encode([
                    "success" => true,
                    "message" => "User deleted successfully."
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "User not found or already deleted."
                ]);
            }
        } catch (PDOException $e) {
            // Handle any database errors
            echo json_encode([
                "success" => false,
                "message" => "Failed to delete user: " . $e->getMessage()
            ]);
        }
    } else {
        // ID is missing in the request
        echo json_encode([
            "success" => false,
            "message" => "User ID is required."
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
