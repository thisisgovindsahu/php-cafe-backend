<?php
// Set the content type to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Clear the user session cookie
    $cookie_name = "user_session";

    // Check if the cookie exists in the request
    if (!isset($_COOKIE[$cookie_name])) {
        // No active session found
        echo json_encode([
            "success" => false,
            "message" => "No active session found."
        ]);
        exit; // Stop further processing
    }

    // Check if the cookie exists
    if (isset($_COOKIE[$cookie_name])) {
        // Expire the cookie by setting its expiry time to the past
        setcookie($cookie_name, "", time() - 3600, "/", "", false, true);

        // Respond with a success message
        echo json_encode([
            "success" => true,
            "message" => "Logout successful."
        ]);
    } else {
        // If the cookie doesn't exist, respond with a failure message
        echo json_encode([
            "success" => false,
            "message" => "No active session found."
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Use POST."
    ]);
}
?>
