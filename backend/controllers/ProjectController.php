<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';
require_once '../models/Project.php';

$database = new Database();
$db = $database->getConnection();

$project = new Project($db);

$request_method = $_SERVER['REQUEST_METHOD'];

switch($request_method) {
    case 'GET':
        if(isset($_GET['id'])) {
            $project->id = $_GET['id'];
            $project->readOne();
            
            if($project->title != null) {
                $project_arr = array(
                    "id" => $project->id,
                    "title" => $project->title,
                    "description" => $project->description,
                    "content" => $project->content,
                    "thumbnail" => $project->thumbnail,
                    "images" => json_decode($project->images),
                    "category" => $project->category,
                    "tags" => explode(',', $project->tags),
                    "live_url" => $project->live_url,
                    "github_url" => $project->github_url,
                    "published" => $project->published,
                    "created_at" => $project->created_at,
                    "updated_at" => $project->updated_at
                );
                
                http_response_code(200);
                echo json_encode($project_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Project not found."));
            }
        } elseif(isset($_GET['search'])) {
            $keywords = $_GET['search'];
            $stmt = $project->search($keywords);
            $num = $stmt->rowCount();
            
            if($num > 0) {
                $projects_arr = array();
                $projects_arr["records"] = array();
                
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $project_item = array(
                        "id" => $id,
                        "title" => $title,
                        "description" => $description,
                        "thumbnail" => $thumbnail,
                        "category" => $category,
                        "tags" => explode(',', $tags),
                        "live_url" => $live_url,
                        "github_url" => $github_url,
                        "created_at" => $created_at
                    );
                    
                    array_push($projects_arr["records"], $project_item);
                }
                
                http_response_code(200);
                echo json_encode($projects_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No projects found."));
            }
        } else {
            $published_only = !isset($_GET['admin']);
            $stmt = $project->readAll($published_only);
            $num = $stmt->rowCount();
            
            if($num > 0) {
                $projects_arr = array();
                $projects_arr["records"] = array();
                
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $project_item = array(
                        "id" => $id,
                        "title" => $title,
                        "description" => $description,
                        "thumbnail" => $thumbnail,
                        "category" => $category,
                        "tags" => explode(',', $tags),
                        "live_url" => $live_url,
                        "github_url" => $github_url,
                        "published" => $published,
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    );
                    
                    array_push($projects_arr["records"], $project_item);
                }
                
                http_response_code(200);
                echo json_encode($projects_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No projects found."));
            }
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if(
            !empty($data->title) &&
            !empty($data->description) &&
            !empty($data->content)
        ) {
            $project->title = $data->title;
            $project->description = $data->description;
            $project->content = $data->content;
            $project->thumbnail = isset($data->thumbnail) ? $data->thumbnail : '';
            $project->images = isset($data->images) ? json_encode($data->images) : '[]';
            $project->category = isset($data->category) ? $data->category : 'Web';
            $project->tags = isset($data->tags) ? implode(',', $data->tags) : '';
            $project->live_url = isset($data->live_url) ? $data->live_url : '';
            $project->github_url = isset($data->github_url) ? $data->github_url : '';
            $project->published = isset($data->published) ? $data->published : 1;
            
            if($project->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Project created successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create project."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create project. Data is incomplete."));
        }
        break;
        
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        
        if(
            !empty($data->id) &&
            !empty($data->title) &&
            !empty($data->description) &&
            !empty($data->content)
        ) {
            $project->id = $data->id;
            $project->title = $data->title;
            $project->description = $data->description;
            $project->content = $data->content;
            $project->thumbnail = isset($data->thumbnail) ? $data->thumbnail : '';
            $project->images = isset($data->images) ? json_encode($data->images) : '[]';
            $project->category = isset($data->category) ? $data->category : 'Web';
            $project->tags = isset($data->tags) ? implode(',', $data->tags) : '';
            $project->live_url = isset($data->live_url) ? $data->live_url : '';
            $project->github_url = isset($data->github_url) ? $data->github_url : '';
            $project->published = isset($data->published) ? $data->published : 1;
            
            if($project->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Project updated successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update project."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to update project. Data is incomplete."));
        }
        break;
        
    case 'DELETE':
        if(isset($_GET['id'])) {
            $project->id = $_GET['id'];
            
            if($project->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Project deleted successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete project."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Missing project ID."));
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
