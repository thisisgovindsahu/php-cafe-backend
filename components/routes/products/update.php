<?php
// Include the database connection and helper files
include "../../../db-conn/connect.php";
include "../../middlewares/isAdmin.php";
include "../../utils/helpers.php";

// Set the content type to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['name']) || empty($_POST['price_type']) || empty($_POST['full_price']) || empty($_POST['category']) ) {
        echo json_encode([
            "success" => false,
            "message" => "Fields are required"
        ]);
        exit;
    }

    $productid = filter_var($_GET['productid'], FILTER_SANITIZE_STRING);

    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $slug = to_slug($name);
    $price_type = filter_var($_POST['price_type'], FILTER_SANITIZE_STRING);
    $half_price = "";
    $full_price = filter_var($_POST['full_price'], FILTER_SANITIZE_STRING);
    $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);

    // delete privious image from image folder
    if (isset($_FILES['image']) && $_FILES['image']['name'] !== "") {
    $pre_image = "SELECT image FROM products WHERE id = ?";
    $image = $conn->prepare($pre_image);
    $image->execute([$productid]);
    $image_name = $image->fetch(PDO::FETCH_ASSOC)['image'];
    $pre_image_path = __DIR__ . "/../../../uploaded_files/" . $image_name;
    unlink($pre_image_path);
    }
    // Handle the optional image upload
    $rename = null;
    if (isset($_FILES['image']) && $_FILES['image']['name'] !== "") {
        $image = $_FILES['image']['name'];
        $image = filter_var($image, FILTER_SANITIZE_STRING);
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $rename = unique_id() . "." . $ext;

        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = __DIR__ . "/../../../uploaded_files/" . $rename;

        if (!is_dir(dirname($image_folder))) {
            mkdir(dirname($image_folder), 0777, true);
        }

        if (!move_uploaded_file($image_tmp_name, $image_folder)) {
            echo json_encode([
                "success" => false,
                "message" => "Failed to upload the image."
            ]);
            exit;
        }
    }
    try {
        $query_string = "UPDATE products SET name = ?, slug = ?, price_type = ?";
        $params = [$name, $slug, $price_type];

        if ($price_type === "both"){
            if(empty($_POST['half_price'])) {
                echo json_encode([
                    "success" => false,
                    "message" => "Half price is required."
                ]);
                exit;
            }
            $half_price = filter_var($_POST['half_price'], FILTER_SANITIZE_STRING);
            $query_string .= ", half_price = ?, full_price = ?, category = ?";
            array_push($params, $half_price, $full_price, $category);
        } else {
            $half_price = "";
            $query_string .= ", half_price = ?, full_price = ?, category = ?";
            array_push($params, $half_price, $full_price, $category);
        }

        if ($rename) {
            $query_string .= ", image = ?";
            $params[] = $rename;
        }

        $query_string .= " WHERE id = ?";
        $params[] = $productid;

        $query = $conn->prepare($query_string);
        $query->execute($params);

        // Check if the update was successful
        if ($query->rowCount() > 0) {
            echo json_encode([
                "success" => true,
                "message" => "Product updated successfully!"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No changes made or product not found."
            ]);
        }
        
    } catch (PDOException $e) {
        // Handle exceptions
        echo json_encode([
            "success" => false,
            "message" => "Failed to update product: " . $e->getMessage()
        ]);
    }
} else {
    // Send a response for unsupported methods
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Use POST for this route."
    ]);
}
