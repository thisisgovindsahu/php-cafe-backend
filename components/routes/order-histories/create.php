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

    if (!isset($data['history'])) {
        http_response_code(422);
        echo json_encode([
            "success" => false,
            "message" => "History details incomplete!"
        ]);
        exit;
    }

    $id = unique_id();

    $history = json_encode($data['history']);

    try {
        
        $order = $conn->prepare("INSERT INTO histories (id, history) VALUES (?, ?)");

        if ($order->execute([$id, $history])) {
            echo json_encode([
                "success" => true,
                "message" => "History saved successfully."
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Failed to get history order."
            ]);
        }

    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Error while getting history order."
        ]);
    }

} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Use POST."
    ]);

}
?>
