<?php
// Simple debug endpoint for Wasmer
error_log("Debug endpoint accessed");

// Test PHP basics
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Current directory: " . __DIR__ . "\n";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] ?? 'Not set' . "\n";

// Test file system
$test_file = __DIR__ . '/../../test.txt';
if (file_put_contents($test_file, 'Test write: ' . date('Y-m-d H:i:s'))) {
    echo "File write: SUCCESS\n";
    echo "File path: " . $test_file . "\n";
} else {
    echo "File write: FAILED\n";
}

// Test PDO
try {
    $pdo = new PDO('sqlite::memory:');
    echo "SQLite in-memory: SUCCESS\n";
} catch (Exception $e) {
    echo "SQLite in-memory: FAILED - " . $e->getMessage() . "\n";
}

// Test environment variables
echo "DATABASE_URL: " . (getenv('DATABASE_URL') ?: 'NOT SET') . "\n";

// Test session
session_start();
echo "Session: STARTED\n";
$_SESSION['test'] = 'value';
echo "Session write: SUCCESS\n";

phpinfo(INFO_MODULES);
?>
