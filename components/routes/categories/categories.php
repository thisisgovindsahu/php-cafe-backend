<?php
// Include the database connection file
include "../../../db-conn/connect.php";

// Set the content type to JSON
header('Content-Type: application/json');

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Fetch all categories from the database
        $fetch_categories = $conn->prepare("SELECT id, name, slug FROM categories ORDER BY time DESC");
        $fetch_categories->execute();

        if ($fetch_categories->rowCount() > 0) {
            $categories = $fetch_categories->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "success" => true,
                "data" => $categories
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No categories found."
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            "success" => false,
            "message" => "Failed to fetch categories: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Use GET."
    ]);
}
?>
