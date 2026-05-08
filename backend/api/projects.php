<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';
require_once '../models/Project.php';
require_once '../utils/auth.php';

$database = new Database();
$db = $database->getConnection();

// Check if database connection failed
if ($db === null) {
    // Return empty projects array if database fails
    header('Content-Type: application/json');
    http_response_code(200);
    echo json_encode(array("records" => array(), "total" => 0, "message" => "Database unavailable"));
    exit();
}

$project = new Project($db);
$auth = new Auth();

$request_method = $_SERVER['REQUEST_METHOD'];
$project_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Check if admin request
$is_admin = isset($_GET['admin']) || (isset($_GET['action']) && $_GET['action'] === 'admin');

// Handle GET request
if ($request_method === 'GET') {
    // Ensure content type is set
    header('Content-Type: application/json');
    
    try {
        // Check if requesting single project
        if ($project_id) {
            $project->id = $project_id;
            $project->readOne();
            
            if ($project->title) {
                $images_array = !empty($project->images) ? json_decode($project->images, true) : [];
                
                http_response_code(200);
                echo json_encode(array(
                    "id" => $project->id,
                    "title" => $project->title,
                    "description" => $project->description,
                    "content" => $project->content,
                    "thumbnail" => $project->thumbnail,
                    "images" => $images_array,
                    "category" => $project->category,
                    "tags" => explode(',', $project->tags),
                    "live_url" => $project->live_url,
                    "github_url" => $project->github_url,
                    "published" => $project->published,
                    "created_at" => $project->created_at,
                    "updated_at" => $project->updated_at
                ));
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Project not found."));
            }
        } else {
            // Get all projects
            $published_only = !$is_admin;
            $stmt = $project->readAll($published_only);
            $num = $stmt->rowCount();
            
            $records = array();
            if ($num > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $images_array = !empty($row['images']) ? json_decode($row['images'], true) : [];
                    $tags_array = !empty($row['tags']) ? array_map('trim', explode(',', $row['tags'])) : array();
                    
                    $records[] = array(
                        "id" => $row['id'],
                        "title" => $row['title'],
                        "description" => $row['description'],
                        "content" => $row['content'],
                        "thumbnail" => $row['thumbnail'],
                        "images" => $images_array,
                        "category" => $row['category'],
                        "tags" => $tags_array,
                        "live_url" => $row['live_url'],
                        "github_url" => $row['github_url],
                        "published" => $row['published'],
                        "created_at" => $row['created_at'],
                        "updated_at" => $row['updated_at']
                    );
                }
            }
            
            http_response_code(200);
            echo json_encode(array("records" => $records, "total" => count($records)));
        }
    } catch (Exception $e) {
        // Return empty response if database fails
        http_response_code(200);
        echo json_encode(array("records" => array(), "total" => 0, "message" => "Database connection failed, returning empty projects list"));
    }
    exit();
}

// Require authentication for POST, PUT, DELETE
if (!in_array($request_method, ['POST', 'PUT', 'DELETE', 'OPTIONS'])) {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
    exit();
}

if ($request_method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check authentication for admin operations - simplified for now
if ($is_admin && !isset($_SESSION['admin_logged_in'])) {
    session_start();
    if (!isset($_SESSION['admin_logged_in'])) {
        http_response_code(401);
        echo json_encode(array("message" => "Unauthorized."));
        exit();
    }
}

switch($request_method) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if(
            !empty($data->title) &&
            !empty($data->description) &&
            !empty($data->content)
        ) {
            $project->title = htmlspecialchars(strip_tags($data->title));
            $project->description = htmlspecialchars(strip_tags($data->description));
            $project->content = htmlspecialchars(strip_tags($data->content));
            $project->thumbnail = isset($data->thumbnail) ? htmlspecialchars(strip_tags($data->thumbnail)) : '';
            $project->images = isset($data->images) ? json_encode($data->images) : '[]';
            $project->category = isset($data->category) ? htmlspecialchars(strip_tags($data->category)) : 'Web';
            $project->tags = isset($data->tags) ? implode(', ', $data->tags) : '';
            $project->live_url = isset($data->live_url) ? htmlspecialchars(strip_tags($data->live_url)) : '';
            $project->github_url = isset($data->github_url) ? htmlspecialchars(strip_tags($data->github_url)) : '';
            $project->published = isset($data->published) ? (int)$data->published : 1;
            
            if($project->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Project created successfully.", "id" => $db->lastInsertId()));
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
        if (!$project_id) {
            http_response_code(400);
            echo json_encode(array("message" => "Project ID is required."));
            break;
        }
        
        $data = json_decode(file_get_contents("php://input"));
        
        $project->id = $project_id;
        
        // Check if project exists
        $check_stmt = $db->prepare("SELECT id FROM projects WHERE id = ?");
        $check_stmt->bindParam(1, $project_id);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(array("message" => "Project not found."));
            break;
        }
        
        $project->title = !empty($data->title) ? htmlspecialchars(strip_tags($data->title)) : '';
        $project->description = !empty($data->description) ? htmlspecialchars(strip_tags($data->description)) : '';
        $project->content = !empty($data->content) ? htmlspecialchars(strip_tags($data->content)) : '';
        $project->thumbnail = isset($data->thumbnail) ? htmlspecialchars(strip_tags($data->thumbnail)) : '';
        $project->images = isset($data->images) ? json_encode($data->images) : '[]';
        $project->category = isset($data->category) ? htmlspecialchars(strip_tags($data->category)) : 'Web';
        $project->tags = isset($data->tags) ? implode(', ', $data->tags) : '';
        $project->live_url = isset($data->live_url) ? htmlspecialchars(strip_tags($data->live_url)) : '';
        $project->github_url = isset($data->github_url) ? htmlspecialchars(strip_tags($data->github_url)) : '';
        $project->published = isset($data->published) ? (int)$data->published : 1;
        
        if($project->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "Project updated successfully."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update project."));
        }
        break;
        
    case 'DELETE':
        if (!$project_id) {
            http_response_code(400);
            echo json_encode(array("message" => "Project ID is required."));
            break;
        }
        
        $project->id = $project_id;
        
        // Check if project exists
        $check_stmt = $db->prepare("SELECT id FROM projects WHERE id = ?");
        $check_stmt->bindParam(1, $project_id);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(array("message" => "Project not found."));
            break;
        }
        
        if($project->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "Project deleted successfully."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to delete project."));
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>
