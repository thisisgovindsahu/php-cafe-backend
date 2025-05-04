<?php

// Set the content type to JSON
header('Content-Type: application/json');

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!empty($data['aid']) || !empty($_GET['aid'])) {
    $activeId = null;
    if (isset($data['aid'])) $activeId = filter_var($data['aid'], FILTER_SANITIZE_STRING);
    else $activeId = filter_var($_GET['aid'], FILTER_SANITIZE_STRING);
    $check_admin = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $check_admin->execute([$activeId]);
try {
    if ($check_admin->rowCount() > 0) {
    // Fetch the user details
    $user = $check_admin->fetch(PDO::FETCH_ASSOC);
    
    if ($user['role'] < 1 && !$user['role']) {
        echo json_encode([
            "success" => false,
            "message" => "Unauthorized access!"
        ]);
        exit;
    }
    
} else {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access!"
    ]);
    exit;
};
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Something went wrong."
    ]);
}
} else {
    echo json_encode([
        "success" => false,
        "message" => "Trying to make Unauthorize access."
    ]);
    exit;
}

?>