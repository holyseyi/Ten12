<?php
// Test database connection and check for users
require_once __DIR__ . '/config/database.php';

echo "Testing database connection...\n";

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    echo "FAILED: Database connection failed\n";
    echo "Please check your database configuration in backend/config/database.php\n";
    echo "Make sure MySQL is running and the database 'ten12_portfolio' exists\n";
    exit(1);
}

echo "SUCCESS: Database connection established\n\n";

// Check if admin_users table exists
try {
    $stmt = $db->query("SHOW TABLES LIKE 'admin_users'");
    if ($stmt->rowCount() === 0) {
        echo "FAILED: admin_users table does not exist\n";
        echo "Please run backend/setup_database.sql to create the database tables\n";
        exit(1);
    }
    echo "SUCCESS: admin_users table exists\n\n";
} catch (PDOException $e) {
    echo "FAILED: Error checking tables: " . $e->getMessage() . "\n";
    exit(1);
}

// Check if users exist
try {
    $stmt = $db->query("SELECT id, username, email, role FROM admin_users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "WARNING: No users found in admin_users table\n";
        echo "Please run backend/setup_database.sql to create the default admin user\n";
        exit(1);
    }
    
    echo "SUCCESS: Found " . count($users) . " user(s):\n";
    foreach ($users as $user) {
        echo "  - ID: {$user['id']}, Username: {$user['username']}, Email: {$user['email']}, Role: {$user['role']}\n";
    }
    echo "\n";
    
    echo "Default login credentials:\n";
    echo "  Username: admin\n";
    echo "  Password: password\n";
    
} catch (PDOException $e) {
    echo "FAILED: Error querying users: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nDatabase setup appears to be correct. If you still cannot login, check:\n";
echo "1. Browser console for JavaScript errors\n";
echo "2. Network tab for failed API requests\n";
echo "3. PHP error logs\n";
?>
