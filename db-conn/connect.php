<?php
try {
    $db_host = "65.109.49.230"; // Or host provided by PowerHost
    $db_name = "webbspi1_menu-app";
    $user_name = "webbspi1_govind";
    $user_password = "TRUQbB9zWoBT";
    
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $user_name, $user_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Remove echo statements in production
    // echo "connected"; 
} catch(PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    // Display generic error message in production
    die("Database connection error");
}
?>