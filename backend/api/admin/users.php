<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
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

// Helper function to check if current user is admin
function isAdmin() {
    return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'admin';
}

// Get current user info
function getCurrentUser() {
    if (isset($_SESSION['admin_id'])) {
        return [
            'id' => $_SESSION['admin_id'],
            'username' => $_SESSION['admin_username'],
            'email' => $_SESSION['admin_email'],
            'role' => $_SESSION['admin_role']
        ];
    }
    return null;
}

switch($request_method) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if (!isAdmin()) {
            http_response_code(403);
            echo json_encode(array("success" => false, "message" => "Unauthorized. Admin access required."));
            exit();
        }
        
        if (!empty($data->username) && !empty($data->password) && !empty($data->email)) {
            // Check if username already exists
            $existing = $user->findByUsername($data->username);
            if ($existing) {
                http_response_code(400);
                echo json_encode(array("success" => false, "message" => "Username already exists."));
                exit();
            }
            
            $role = isset($data->role) ? $data->role : 'user';
            $id = $user->create($data->username, $data->password, $data->email, $role);
            
            if ($id) {
                http_response_code(201);
                echo json_encode(array(
                    "success" => true,
                    "message" => "User created successfully.",
                    "user" => [
                        "id" => $id,
                        "username" => $data->username,
                        "email" => $data->email,
                        "role" => $role
                    ]
                ));
            } else {
                http_response_code(500);
                echo json_encode(array("success" => false, "message" => "Failed to create user."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("success" => false, "message" => "Username, password, and email are required."));
        }
        break;
        
    case 'GET':
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            http_response_code(401);
            echo json_encode(array("success" => false, "message" => "Not authenticated."));
            exit();
        }
        
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        
        if ($id) {
            $result = $user->findById($id);
            if ($result) {
                unset($result['password']);
                echo json_encode(array("success" => true, "user" => $result));
            } else {
                http_response_code(404);
                echo json_encode(array("success" => false, "message" => "User not found."));
            }
        } else {
            if (!isAdmin()) {
                http_response_code(403);
                echo json_encode(array("success" => false, "message" => "Unauthorized."));
                exit();
            }
            
            $stmt = $user->getAll();
            $users = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                unset($row['password']);
                $users[] = $row;
            }
            echo json_encode(array("success" => true, "users" => $users));
        }
        break;
        
    case 'PUT':
        if (!isAdmin()) {
            http_response_code(403);
            echo json_encode(array("success" => false, "message" => "Unauthorized. Admin access required."));
            exit();
        }
        
        $data = json_decode(file_get_contents("php://input"));
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        
        if ($id && $data) {
            $updateData = [
                'username' => $data->username ?? '',
                'email' => $data->email ?? '',
                'role' => $data->role ?? 'user'
            ];
            
            $result = $user->update($id, $updateData);
            
            // Update password if provided
            if (!empty($data->password)) {
                $user->updatePassword($id, $data->password);
            }
            
            if ($result) {
                echo json_encode(array("success" => true, "message" => "User updated successfully."));
            } else {
                http_response_code(500);
                echo json_encode(array("success" => false, "message" => "Failed to update user."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("success" => false, "message" => "Invalid request."));
        }
        break;
        
    case 'DELETE':
        if (!isAdmin()) {
            http_response_code(403);
            echo json_encode(array("success" => false, "message" => "Unauthorized. Admin access required."));
            exit();
        }
        
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        
        if ($id) {
            // Prevent deleting yourself
            if ($id == $_SESSION['admin_id']) {
                http_response_code(400);
                echo json_encode(array("success" => false, "message" => "Cannot delete your own account."));
                exit();
            }
            
            $result = $user->delete($id);
            
            if ($result) {
                echo json_encode(array("success" => true, "message" => "User deleted successfully."));
            } else {
                http_response_code(500);
                echo json_encode(array("success" => false, "message" => "Failed to delete user."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("success" => false, "message" => "User ID is required."));
        }
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