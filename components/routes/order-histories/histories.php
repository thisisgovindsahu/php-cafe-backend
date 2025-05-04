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
        $fetch_histories = $conn->prepare("SELECT id, history FROM histories ORDER BY time DESC");
        $fetch_histories->execute();

        if ($fetch_histories->rowCount() > 0) {
            $histories = $fetch_histories->fetchAll(PDO::FETCH_ASSOC);

            $histories_data = [];
            foreach ($histories as $data) {
                $data['history'] = json_decode($data['history'], true);
                $histories_data[] = $data;
            }

            echo json_encode([
                "success" => true,
                "data" => $histories
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No histories found."
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            "success" => false,
            "message" => "Failed to fetch histories: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Use GET."
    ]);
}
?>
