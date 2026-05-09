<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

error_log("=== Supabase Test Started ===");

// Test database connection
require_once __DIR__ . '/config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    error_log("Database connection: SUCCESS");
    
    // Test if admin_users table exists
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM admin_users");
        $result = $stmt->fetch();
        error_log("Admin users count: " . $result['count']);
        
        // Test user lookup
        $user = $db->query("SELECT * FROM admin_users WHERE username = 'admin'")->fetch();
        if ($user) {
            error_log("Admin user found: " . json_encode($user));
        } else {
            error_log("Admin user NOT found");
        }
        
        echo json_encode([
            "success" => true,
            "message" => "Database connected successfully",
            "admin_users" => $result['count'],
            "database_url" => getenv('DATABASE_URL') ?: 'NOT SET'
        ]);
    } catch (Exception $e) {
        error_log("Database query error: " . $e->getMessage());
        echo json_encode([
            "success" => false,
            "message" => "Database query failed: " . $e->getMessage()
        ]);
    }
} else {
    error_log("Database connection: FAILED");
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed",
        "database_url" => getenv('DATABASE_URL') ?: 'NOT SET'
    ]);
}
?>
