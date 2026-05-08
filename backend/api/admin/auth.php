<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

$request_method = $_SERVER['REQUEST_METHOD'];

// Get database connection
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(array("success" => false, "message" => "Database connection failed."));
    exit();
}

$user = new User($db);

switch($request_method) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->username) && !empty($data->password)) {
            $auth_user = $user->verifyPassword($data->username, $data->password);
            
            if($auth_user) {
                // Set session
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $auth_user['id'];
                $_SESSION['admin_username'] = $auth_user['username'];
                $_SESSION['admin_email'] = $auth_user['email'];
                $_SESSION['admin_role'] = $auth_user['role'];
                
                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "message" => "Login successful",
                    "user" => array(
                        "id" => $auth_user['id'],
                        "username" => $auth_user['username'],
                        "email" => $auth_user['email'],
                        "role" => $auth_user['role']
                    )
                ));
            } else {
                http_response_code(401);
                echo json_encode(array("success" => false, "message" => "Invalid credentials"));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("success" => false, "message" => "Username and password are required."));
        }
        break;
        
    case 'GET':
        // Session check
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            http_response_code(200);
            echo json_encode(array(
                "success" => true,
                "message" => "Authenticated",
                "user" => array(
                    "id" => $_SESSION['admin_id'],
                    "username" => $_SESSION['admin_username'],
                    "email" => $_SESSION['admin_email'],
                    "role" => $_SESSION['admin_role']
                )
            ));
        } else {
            http_response_code(401);
            echo json_encode(array("success" => false, "message" => "Not authenticated"));
        }
        break;
        
    case 'DELETE':
        // Logout
        session_destroy();
        echo json_encode(array("success" => true, "message" => "Logged out successfully"));
        break;
        
    case 'OPTIONS':
        http_response_code(200);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(array("success" => false, "message" => "Method not allowed."));
        break;
}
?>