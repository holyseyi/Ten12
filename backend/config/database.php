<?php
class Database {
    private $db_file;
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // Check for environment variable for database URL (for deployment)
            $database_url = getenv('DATABASE_URL') ?: getenv('POSTGRES_URL') ?: getenv('MYSQL_URL');

            error_log("DATABASE_URL from env: " . ($database_url ? 'SET' : 'NOT SET'));

            if ($database_url) {
                // Use PostgreSQL/MySQL from environment variable
                $this->conn = new PDO($database_url);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                error_log("Connected via DATABASE_URL");
            } else {
                // Fallback to SQLite for local development or serverless
                $this->db_file = __DIR__ . '/../../database.sqlite';
                $db_dir = dirname($this->db_file);

                // Check if directory is writable (for serverless environments)
                $use_in_memory = false;
                if (is_dir($db_dir) && is_writable($db_dir)) {
                    error_log("Attempting SQLite at: " . $this->db_file);
                    $dsn = "sqlite:" . $this->db_file;
                } else {
                    error_log("Directory not writable, using in-memory SQLite");
                    $dsn = "sqlite::memory:";
                    $use_in_memory = true;
                }

                $this->conn = new PDO($dsn);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                error_log("SQLite connection successful");

                // Initialize database if it doesn't exist (or always for in-memory)
                if ($use_in_memory || !file_exists($this->db_file)) {
                    $this->initializeDatabase();
                }
            }
        } catch(PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
            return null;
        }

        return $this->conn;
    }

    private function initializeDatabase() {
        // Check if tables exist
        $stmt = $this->conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='admin_users'");
        if ($stmt->fetch() === false) {
            // Create tables
            $this->conn->exec("
                CREATE TABLE IF NOT EXISTS projects (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT NOT NULL,
                    description TEXT NOT NULL,
                    content TEXT NOT NULL,
                    thumbnail TEXT,
                    images TEXT,
                    category TEXT DEFAULT 'Web',
                    tags TEXT,
                    live_url TEXT,
                    github_url TEXT,
                    published INTEGER DEFAULT 1,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS admin_users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username TEXT UNIQUE NOT NULL,
                    password TEXT NOT NULL,
                    email TEXT UNIQUE NOT NULL,
                    role TEXT DEFAULT 'user',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS contacts (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    email TEXT NOT NULL,
                    message TEXT NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS settings (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    setting_key TEXT UNIQUE NOT NULL,
                    setting_value TEXT,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );
            ");

            // Insert default admin user (password: 'password')
            $password_hash = password_hash('password', PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("INSERT INTO admin_users (username, password, email, role) VALUES (?, ?, ?, ?)");
            $stmt->execute(['admin', $password_hash, 'admin@ten12.com', 'admin']);

            // Insert default settings
            $stmt = $this->conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
            $stmt->execute(['site_title', 'Ten12 Portfolio']);
            $stmt->execute(['site_description', 'Modern. Memorable. Ten12.']);
            $stmt->execute(['contact_email', 'contact@ten12.com']);
            $stmt->execute(['social_links', '{"github": "#", "linkedin": "#", "twitter": "#"}']);
        }
    }
}
?>
