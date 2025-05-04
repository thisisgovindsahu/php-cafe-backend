<?php
// Include the database connection file
include "../../../db-conn/connect.php";
include "../../utils/helpers.php";

// Set the content type to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['name']) && !empty($_POST['phone']) && !empty($_POST['password'])) {
        $id = unique_id();

        // Sanitize the inputs
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
        $password = password_hash(filter_var($_POST['password'], FILTER_SANITIZE_STRING), PASSWORD_DEFAULT);

        try {
            // Check if the phone number is already registered
            $check_phone = $conn->prepare("SELECT * FROM users WHERE phone = ?");
            $check_phone->execute([$phone]);

            if ($check_phone->rowCount() > 0) {
                // If the phone number already exists, return an error response
                echo json_encode([
                    "success" => false,
                    "message" => "Mobile number is already registered."
                ]);
            } else {
                $query = "INSERT INTO users (id, name, phone, password) VALUES (?, ?, ?, ?)";
                $user_data = [$id, $name, $phone, $password];
                $is_first_user = false;
            
                $check_first_user = $conn->prepare("SELECT * FROM users");
                $check_first_user->execute();
                if ($check_first_user->rowCount() == 0) {
                    $is_first_user = true;
                    $query = "INSERT INTO users (id, name, phone, password, role) VALUES (?, ?, ?, ?, ?)";
                    $user_data = [$id, $name, $phone, $password, 2];
                }
                
                // If the phone number is unique, insert the user into the database
                $insert_user = $conn->prepare($query);

                if ($insert_user->execute($user_data)) {
                    echo json_encode([
                        "success" => true,
                        "message" => "User registered successfully."
                    ]);
                } else {
                    echo json_encode([
                        "success" => false,
                        "message" => "Failed to register user."
                    ]);
                }
            }
            
        } catch (PDOException $e) {
            echo json_encode([
                "success" => false,
                "message" => "Failed to register: " . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "All fields are required!"
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Use POST."
    ]);
}
?>
