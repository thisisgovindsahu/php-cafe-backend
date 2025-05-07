<?php
// Include the database connection and helper files
include "../../../db-conn/connect.php";
include "../../middlewares/isAdmin.php";

// Set the content type to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw input data
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    $userid = filter_var($_GET['userid'], FILTER_SANITIZE_STRING);
    $id = filter_var($data['aid']);
    $role = 1;

    try {
        $check_role = $conn->prepare("SELECT role FROM users WHERE id = ?");
        $check_role->execute([$id]);

        if ($check_role->fetch(PDO::FETCH_ASSOC) < 2) {
            echo json_encode([
                "success" => false,
                "message" => "Unauthorized access!"
            ]);
        } else {
        
        $query_string = "UPDATE users SET role = ? WHERE id = ?";
        $query = $conn->prepare($query_string);
        $query->execute([$role, $userid]);

        // Check if the update was successful
        if ($query->rowCount() > 0) {
            echo json_encode([
                "success" => true,
                "message" => "User updated successfully!"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No changes made or user not found."
            ]);
        }
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
