<?php
// Include the database connection file
include "../../../db-conn/connect.php";
include "../../middlewares/isAdmin.php";

// Set the content type to JSON
header('Content-Type: application/json');
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Connection: keep-alive");

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Fetch all orders from the database
        $fetch_orders = $conn->prepare("SELECT id, products, buyer, tableNumber, total FROM orders ORDER BY time DESC");
        $fetch_orders->execute();

        if ($fetch_orders->rowCount() > 0) {
            $orders = $fetch_orders->fetchAll(PDO::FETCH_ASSOC);

            $order_data = [];
            foreach ($orders as $data) {
                $data['products'] = json_decode($data['products'], true);
                $order_data[] = $data;
            }

            echo json_encode([
                "success" => true,
                "data" => $order_data
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No orders found."
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            "success" => false,
            "message" => "Failed to fetch orders: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Use GET."
    ]);
}
?>
