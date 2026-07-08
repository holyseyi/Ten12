<?php
session_start();
$_SESSION['admin_logged_in'] = true;

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Project.php';

// Test database connection
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Database connection failed");
}

echo "Database connected!\n";

// Test creating a project
$project = new Project($db);
$project->title = "Test E-commerce Platform";
$project->description = "A test e-commerce platform";
$project->content = "This is a test project for e-commerce";
$project->thumbnail = "";
$project->images = "[]";
$project->category = "Web";
$project->tags = "PHP, MySQL, E-commerce";
$project->live_url = "https://test.com";
$project->github_url = "https://github.com/test/repo";
$project->published = 1;

if ($project->create()) {
    echo "Project created successfully!\n";
    echo "Last insert ID: " . $db->lastInsertId() . "\n";
} else {
    echo "Project creation failed!\n";
}

// Test reading all projects
$stmt = $project->readAll(false);
$count = $stmt->rowCount();
echo "Total projects: " . $count . "\n";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "Project: " . $row['title'] . "\n";
}
?>
