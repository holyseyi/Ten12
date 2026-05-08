<?php
session_start();

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized"));
    exit();
}

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$request_method = $_SERVER['REQUEST_METHOD'];

switch($request_method) {
    case 'GET':
        // Get all admin users
        $query = "SELECT id, username, email, created_at FROM admin_users ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $users = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $row;
        }
        
        http_response_code(200);
        echo json_encode(array("users" => $users));
        break;
        
    case 'POST':
        // Create new admin user
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->username) && !empty($data->password) && !empty($data->email)) {
            // Check if username or email already exists
            $check_query = "SELECT id FROM admin_users WHERE username = ? OR email = ?";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->bindParam(1, $data->username);
            $check_stmt->bindParam(2, $data->email);
            $check_stmt->execute();
            
            if($check_stmt->rowCount() > 0) {
                http_response_code(400);
                echo json_encode(array("message" => "Username or email already exists"));
                break;
            }
            
            // Hash password
            $hashed_password = password_hash($data->password, PASSWORD_DEFAULT);
            
            // Insert new user
            $query = "INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $data->username);
            $stmt->bindParam(2, $hashed_password);
            $stmt->bindParam(3, $data->email);
            
            if($stmt->execute()) {
                http_response_code(201);
                echo json_encode(array(
                    "message" => "Admin user created successfully",
                    "user" => array(
                        "id" => $db->lastInsertId(),
                        "username" => $data->username,
                        "email" => $data->email
                    )
                ));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create admin user"));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Username, password, and email are required"));
        }
        break;
        
    case 'DELETE':
        // Delete admin user
        $user_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        
        if($user_id && $user_id !== $_SESSION['admin_id']) { // Prevent self-deletion
            $query = "DELETE FROM admin_users WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $user_id);
            
            if($stmt->execute()) {
                http_response_code(200);
                echo json_encode(array("message" => "Admin user deleted successfully"));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete admin user"));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Invalid user ID or cannot delete yourself"));
        }
        break;
        
    case 'OPTIONS':
        http_response_code(200);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}