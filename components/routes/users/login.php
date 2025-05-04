<?php
// Include the database connection file
include "../../../db-conn/connect.php";

// Set the content type to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['phone']) && !empty($_POST['password'])) {

        // Sanitize the inputs
        $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
        $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

        try {
            // Check if the phone number exists in the database
            $check_user = $conn->prepare("SELECT * FROM users WHERE phone = ?");
            $check_user->execute([$phone]);

            if ($check_user->rowCount() > 0) {
                // Fetch the user details
                $user = $check_user->fetch(PDO::FETCH_ASSOC);

                // Verify the password
                if (password_verify($password, $user['password'])) {

                     // Set a cookie for the user session
                     $cookie_name = "user_session";
                     $cookie_value = base64_encode(json_encode([
                         "id" => $user['id'],
                         "name" => $user['name'],
                         "phone" => $user['phone']
                     ]));
                     $cookie_expiry = time() + (86400 * 7); // Cookie valid for 7 days
 
                     setcookie($cookie_name, $cookie_value, $cookie_expiry, "/", "", false, true);
                    

                    // Password matches
                    echo json_encode([
                        "success" => true,
                        "message" => "Login successfully.",
                       "name" => $cookie_name,
                       "value" => $cookie_value,
                       "data" => $user
                    ]);
                } else {
                    // Password does not match
                    echo json_encode([
                        "success" => false,
                        "message" => "Mobile number or password is incorrect."
                    ]);
                }
            } else {
                // Phone number not found
                echo json_encode([
                    "success" => false,
                    "message" => "Mobile number or password is incorrect."
                ]);
            }
            
        } catch (PDOException $e) {
            echo json_encode([
                "success" => false,
                "message" => "Failed to login: " . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Phone and password are required!"
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Use POST."
    ]);
}
?>
