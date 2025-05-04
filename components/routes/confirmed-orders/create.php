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

    if (empty($data['confirmOrders'])) {
        http_response_code(422);
        echo json_encode([
            "success" => false,
            "message" => "Confirmed Order details incomplete!"
        ]);
        exit;
    }

    $id = unique_id();

    $confirmOrders = json_encode($data['confirmOrders']);

    try {
        
        $order = $conn->prepare("INSERT INTO confirms (id, confirmOrders) VALUES (?, ?)");

        if ($order->execute([$id, $confirmOrders])) {
            echo json_encode([
                "success" => true,
                "message" => "Order confirmed successfully."
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Failed to confirm order."
            ]);
        }

    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Error while confirming order."
        ]);
    }

} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Use POST."
    ]);

}
?>
