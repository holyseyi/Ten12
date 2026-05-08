<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$request_method = $_SERVER['REQUEST_METHOD'];

switch($request_method) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->username) && !empty($data->password)) {
            // Simple authentication - accept admin/password for now
            if($data->username === 'admin' && $data->password === 'password') {
                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "token" => "session-token",
                    "message" => "Login successful",
                    "user" => array(
                        "id" => 1,
                        "username" => "admin"
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
        // Simple session check for GET requests
        http_response_code(200);
        echo json_encode(array(
            "message" => "Authenticated",
            "user" => array("id" => 1, "username" => "admin")
        ));
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
