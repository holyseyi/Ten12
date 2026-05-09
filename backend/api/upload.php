<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/auth.php';

// Check authentication
$auth = new Auth();
if (!$auth->isAuthenticated()) {
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized."));
    exit();
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $upload_dir = '../uploads/';
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        $max_size = 5 * 1024 * 1024; // 5MB

        // Validate file type
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_types)) {
            http_response_code(400);
            echo json_encode(array("message" => "Invalid file type. Only JPG, PNG, GIF, and WebP are allowed."));
            exit();
        }

        // Validate file size
        if ($file['size'] > $max_size) {
            http_response_code(400);
            echo json_encode(array("message" => "File too large. Maximum size is 5MB."));
            exit();
        }

        // Generate unique filename
        $filename = uniqid() . '.' . $file_ext;
        $upload_path = $upload_dir . $filename;

        // Create upload directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Return the relative URL
            $url = 'uploads/' . $filename;
            http_response_code(200);
            echo json_encode(array(
                "message" => "File uploaded successfully.",
                "url" => $url,
                "filename" => $filename
            ));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Failed to upload file."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "No file uploaded or upload error."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
}
?>
