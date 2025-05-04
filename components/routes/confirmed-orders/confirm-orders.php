<?php
// Include the database connection file
include "../../../db-conn/connect.php";
include "../../middlewares/isAdmin.php";

// Set the content type to JSON
header('Content-Type: application/json');

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Fetch all orders from the database
        $fetch_confirmed_orders = $conn->prepare("SELECT id, confirmOrders FROM confirms ORDER BY time DESC");
        $fetch_confirmed_orders->execute();

        if ($fetch_confirmed_orders->rowCount() > 0) {
            $confirmed_orders = $fetch_confirmed_orders->fetchAll(PDO::FETCH_ASSOC);

            $confirmed_order_data1 = [];
            foreach ($confirmed_orders as $data) {
                $data['confirmOrders'] = json_decode($data['confirmOrders'], true);
                $confirmed_order_data1[] = $data;
            }

            echo json_encode([
                "success" => true,
                "data" => $confirmed_order_data1
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No confirmed orders found."
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            "success" => false,
            "message" => "Failed to fetch confirmed orders: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Use GET."
    ]);
}
?>
