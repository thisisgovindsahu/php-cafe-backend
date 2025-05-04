<?php
// Include the database connection file
include "../../../db-conn/connect.php";
include "../../middlewares/isAdmin.php";
include "../../utils/helpers.php";

// Set the content type to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['name']) && !empty($_POST['full_price']) && !empty($_POST['price_type']) && !empty($_POST['category']) && !empty($_FILES['image'])) {

        $id = unique_id();

        // Sanitize the inputs
        $product_name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $product_slug = to_slug($product_name);
        $price_type = filter_var($_POST['price_type'], FILTER_SANITIZE_STRING);
        $full_price = filter_var($_POST['full_price'], FILTER_SANITIZE_STRING);
        $half_price = "";
        $product_category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);

        // Handle the file upload
        $image = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $rename = $id . "." . $ext;
        $image_folder = __DIR__ . "/../../../uploaded_files/" . $rename;

        try {
            // Insert into the database
            $query = "INSERT INTO products (id, name, slug, price_type";
            $params = [$id, $product_name, $product_slug, $price_type];

            if ($price_type === "both"){
                if(empty($_POST['half_price'])) {
                    echo json_encode([
                        "success" => false,
                        "message" => "Half price is required."
                    ]);
                    exit;
                }
                $half_price = filter_var($_POST['half_price'], FILTER_SANITIZE_STRING);
                $query .= ", half_price, full_price, category, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                array_push($params, $half_price, $full_price, $product_category, $rename);
            } else {
                $query .= ", full_price, category, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
                array_push($params, $full_price, $product_category, $rename);
            }

            $insert_product = $conn->prepare($query);
            $insert_product->execute($params);

            // Move the uploaded file to the desired folder
            if (move_uploaded_file($image_tmp_name, $image_folder)) {
                echo json_encode([
                    "success" => true,
                    "message" => "Product created successfully."
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Failed to upload the image."
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                "success" => false,
                "message" => "Failed to create product: " . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "All fields are required, including the image."
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Use POST."
    ]);
}
?>
