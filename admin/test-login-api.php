<?php
/**
 * Login Test API Endpoint
 * 
 * Usage:
 * POST /admin/test-login-api.php
 * 
 * JSON Body:
 * {
 *   "username": "admin",
 *   "password": "password123",
 *   "debug": true
 * }
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

// Enable logging
$log_file = __DIR__ . '/../backend/logs/login_api.log';
if (!is_dir(dirname($log_file))) {
    mkdir(dirname($log_file), 0755, true);
}
ini_set('error_log', $log_file);

$response = [
    'success' => false,
    'message' => 'Unknown error',
    'debug' => [
        'timestamp' => date('Y-m-d H:i:s'),
        'environment' => []
    ]
];

try {
    // Get input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $response['message'] = 'Invalid JSON input';
        throw new Exception('Invalid JSON');
    }
    
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';
    $debug = $input['debug'] ?? false;
    
    if ($debug) {
        $response['debug']['input_received'] = [
            'username' => $username,
            'password' => '***' . (strlen($password) > 0 ? substr($password, -2) : '')
        ];
    }
    
    // Validation
    if (!$username || !$password) {
        $response['message'] = 'Username and password are required';
        throw new Exception('Missing credentials');
    }
    
    // Load dependencies
    require_once __DIR__ . '/../backend/config/database.php';
    require_once __DIR__ . '/../backend/models/User.php';
    
    // Get database
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        $response['message'] = 'Database connection failed';
        $response['debug']['database_status'] = 'FAILED';
        throw new Exception('Database connection failed');
    }
    
    $response['debug']['database_status'] = 'CONNECTED';
    
    // Count users
    $result = $db->query("SELECT COUNT(*) as count FROM admin_users");
    $count = $result->fetch(PDO::FETCH_ASSOC)['count'];
    $response['debug']['total_users'] = $count;
    
    if ($count === 0) {
        $response['message'] = 'No admin users found in database';
        throw new Exception('No admin users');
    }
    
    // Attempt login
    $user = new User($db);
    $auth_user = $user->verifyPassword($username, $password);
    
    if ($auth_user) {
        session_start();
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $auth_user['id'];
        $_SESSION['admin_username'] = $auth_user['username'];
        $_SESSION['admin_email'] = $auth_user['email'];
        $_SESSION['admin_role'] = $auth_user['role'];
        
        $response['success'] = true;
        $response['message'] = 'Login successful';
        $response['user'] = [
            'id' => $auth_user['id'],
            'username' => $auth_user['username'],
            'email' => $auth_user['email'],
            'role' => $auth_user['role']
        ];
        
        if ($debug) {
            $response['debug']['session_id'] = session_id();
            $response['debug']['login_status'] = 'SUCCESS';
        }
        
        error_log("API Login successful for: $username");
    } else {
        $response['message'] = 'Invalid credentials (username or password incorrect)';
        
        if ($debug) {
            // Check if user exists
            $existing = $user->findByUsername($username);
            $response['debug']['user_exists'] = $existing ? true : false;
            $response['debug']['login_status'] = 'FAILED';
            
            if (!$existing) {
                $response['message'] .= ' [User does not exist]';
            } else {
                $response['message'] .= ' [User exists, password incorrect]';
            }
        }
        
        error_log("API Login failed for: $username");
        throw new Exception('Invalid credentials');
    }
    
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    
    if (!$response['success']) {
        http_response_code(400);
    }
}

// Add environment info if debug
if ($debug && isset($input['debug']) && $input['debug']) {
    $response['debug']['php_version'] = phpversion();
    $response['debug']['database_url_set'] = !empty(getenv('DATABASE_URL'));
    $response['debug']['error_log_location'] = $log_file;
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
