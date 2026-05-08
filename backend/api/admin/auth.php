<?php
session_start();

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';

$request_method = $_SERVER['REQUEST_METHOD'];

switch($request_method) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->username) && !empty($data->password)) {
            // Database authentication
            $database = new Database();
            $db = $database->getConnection();
            
            if($db) {
                $query = "SELECT id, username, password FROM admin_users WHERE username = ? LIMIT 0,1";
                $stmt = $db->prepare($query);
                $stmt->bindParam(1, $data->username);
                $stmt->execute();
                
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($row && password_verify($data->password, $row['password'])) {
                    // Set PHP session
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $row['id'];
                    $_SESSION['admin_username'] = $row['username'];
                    
                    http_response_code(200);
                    echo json_encode(array(
                        "success" => true,
                        "message" => "Login successful",
                        "user" => array(
                            "id" => $row['id'],
                            "username" => $row['username']
                        )
                    ));
                } else {
                    http_response_code(401);
                    echo json_encode(array("success" => false, "message" => "Invalid credentials"));
                }
            } else {
                http_response_code(500);
                echo json_encode(array("success" => false, "message" => "Database connection failed"));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("success" => false, "message" => "Username and password are required."));
        }
        break;
        
    case 'GET':
        // Check session
        if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            http_response_code(200);
            echo json_encode(array(
                "authenticated" => true,
                "user" => array(
                    "id" => $_SESSION['admin_id'],
                    "username" => $_SESSION['admin_username']
                )
            ));
        } else {
            http_response_code(401);
            echo json_encode(array("authenticated" => false, "message" => "Not authenticated"));
        }
        break;
        
    case 'OPTIONS':
        http_response_code(200);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>
