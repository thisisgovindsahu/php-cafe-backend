<?php
// Include the database connection and helper files
include "../../../db-conn/connect.php";
include "../../middlewares/isAdmin.php";
include "../../utils/helpers.php";

// Set the content type to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get the raw input data
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    

    if (!isset($data['name'])) {
        echo json_encode([
            "success" => false,
            "message" => "New category name is required."
        ]);
        exit;
    }

    try {
        $name = filter_var($data['name']);
        $slug = to_slug($name);

        // Get url data
        $category_id = filter_var($_GET['id'], FILTER_SANITIZE_STRING);

        $query_string = "UPDATE categories SET name = ?, slug = ? WHERE id = ?";
        $query = $conn->prepare($query_string);

        // Check if the update was successful
        if ($query->execute([$name, $slug, $category_id])) {
            echo json_encode([
                "success" => true,
                "message" => "Category name updated successfully!"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Something went wrong in new category name."
            ]);
        }
    } catch (PDOException $e) {
        // Handle exceptions
        echo json_encode([
            "success" => false,
            "message" => "Failed to update user: " . $e->getMessage()
        ]);
    }
} else {
    // Send a response for unsupported methods
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Use POST for this route."
    ]);
}
