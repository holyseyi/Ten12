<?php
// Test database connection and check for users
require_once __DIR__ . '/config/database.php';

echo "Testing database connection...\n";

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    echo "FAILED: Database connection failed\n";
    exit(1);
}

echo "SUCCESS: Database connection established\n\n";

// Check if users exist
try {
    $stmt = $db->query("SELECT id, username, email, role FROM admin_users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($users)) {
        echo "WARNING: No users found in admin_users table\n";
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

echo "\nDatabase setup is correct. You can now login.\n";
?>
