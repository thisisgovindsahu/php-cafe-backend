<?php
// Include the database connection file
include "../../../db-conn/connect.php";
include "../../middlewares/isAdmin.php";
include "../../utils/helpers.php";

// Set the content type to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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
            $id = unique_id();
            $name = filter_var($data['name'], FILTER_SANITIZE_STRING);
            $slug = to_slug($name);

            $checkCategoryName = $conn->prepare("SELECT * FROM categories WHERE  name = ?");
            $checkCategoryName->execute([$name]);

            if ($checkCategoryName->rowCount() > 0) {
                echo json_encode([
                    "success" => false,
                    "message" => "Category name already exits!"
                ]);
            } else {
                // Insert into the database
            $insert_categories = $conn->prepare("INSERT INTO categories (id, name, slug) VALUES (?, ?, ?)");
            
            if ($insert_categories->execute([$id, $name, $slug])) {
                echo json_encode([
                    "success" => true,
                    "message" => "New category created successfully."
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Failed to create a new category."
                ]);
            }
            }
        } catch (PDOException $e) {
            echo json_encode([
                "success" => false,
                "message" => "Failed to create category: " . $e->getMessage()
            ]);
        }


    
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Use POST."
    ]);
}
?>
