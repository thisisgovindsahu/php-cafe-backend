<?php
// Include the database connection file
include "../../../db-conn/connect.php";
include "../../utils/helpers.php";

// Set the content type to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    if (empty($data['products'])|| empty($data['tableNumber']) || empty($data['total'])) {
        http_response_code(422);
        echo json_encode([
            "success" => false,
            "message" => "Order details incomplete!"
        ]);
        exit;
    }

    $id = unique_id();

    $products = json_encode($data['products']);
    $buyer = filter_var($data['buyer'], FILTER_SANITIZE_STRING);
    $tableNumber = filter_var($data['tableNumber'], FILTER_SANITIZE_STRING);
    $total = filter_var($data['total'], FILTER_SANITIZE_STRING);

    try {
        
        $order = $conn->prepare("INSERT INTO orders (id, products, buyer, tableNumber, total) VALUES (?, ?, ?, ?, ?)");

        if ($order->execute([$id, $products, $buyer, $tableNumber, $total])) {
            echo json_encode([
                "success" => true,
                "message" => "Order successfully done."
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Failed to make order."
            ]);
        }

    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Error while making order."
        ]);
    }

} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Use POST."
    ]);

}
?>
