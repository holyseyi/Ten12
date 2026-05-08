<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';
require_once '../models/Contact.php';

$database = new Database();
$db = $database->getConnection();

$contact = new Contact($db);

$request_method = $_SERVER['REQUEST_METHOD'];

switch($request_method) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if(
            !empty($data->name) &&
            !empty($data->email) &&
            !empty($data->message)
        ) {
            $contact->name = htmlspecialchars(strip_tags($data->name));
            $contact->email = htmlspecialchars(strip_tags($data->email));
            $contact->message = htmlspecialchars(strip_tags($data->message));
            
            if($contact->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Contact message sent successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to send contact message."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to send contact message. Data is incomplete."));
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
