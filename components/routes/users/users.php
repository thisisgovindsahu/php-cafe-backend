<?php
// Include the database connection file
include "../../../db-conn/connect.php";

// Set the content type to JSON
header('Content-Type: application/json');

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Fetch all users from the database
        $fetch_users = $conn->prepare("SELECT id, name, phone, role FROM users");
        $fetch_users->execute();

        if ($fetch_users->rowCount() > 0) {
            $users = $fetch_users->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "success" => true,
                "data" => $users
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No users found."
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            "success" => false,
            "message" => "Failed to fetch users: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Use GET."
    ]);
}
?>
