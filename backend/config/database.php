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
                // Use PostgreSQL/MySQL/SQLite from environment variable
                $connectionInfo = $this->parseDatabaseUrl($database_url);

                if (!$connectionInfo) {
                    error_log("Unsupported DATABASE_URL scheme: " . $database_url);
                    return null;
                }

                try {
                    $this->conn = new PDO(
                        $connectionInfo['dsn'],
                        $connectionInfo['user'],
                        $connectionInfo['pass'],
                        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
                    );
                    error_log("Connected via DATABASE_URL");

                    // Test connection with simple query
                    $this->conn->query("SELECT 1");
                    error_log("DATABASE_URL connection test: SUCCESS");
                } catch (PDOException $e) {
                    error_log("DATABASE_URL connection failed: " . $e->getMessage());
                    error_log("DATABASE_URL provided: " . substr($database_url, 0, strpos($database_url, '@') + 1) . "***");
                    return null;
                }
            } else {
                // Use file-based SQLite for local development
                // Create data directory if it doesn't exist
                $db_dir = __DIR__ . '/../../data';
                if (!is_dir($db_dir)) {
                    mkdir($db_dir, 0755, true);
                }
                
                $db_file = $db_dir . '/app.db';
                error_log("Using file-based SQLite at: " . $db_file);
                $dsn = "sqlite:" . $db_file;

                $this->conn = new PDO($dsn);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                error_log("SQLite file connection successful");

                // Initialize database if new
                $this->initializeDatabase();
            }
        } catch(PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
            return null;
        }

        return $this->conn;
    }

    private function parseDatabaseUrl(string $database_url): ?array {
        if (stripos($database_url, 'sqlite:') === 0) {
            $sqlitePath = substr($database_url, 7);
            if (strpos($sqlitePath, '///') === 0) {
                $sqlitePath = substr($sqlitePath, 2);
            } elseif (strpos($sqlitePath, '//') === 0) {
                $sqlitePath = substr($sqlitePath, 1);
            }

            if (empty($sqlitePath)) {
                return null;
            }

            return [
                'dsn' => 'sqlite:' . $sqlitePath,
                'user' => null,
                'pass' => null,
            ];
        }

        $url = parse_url($database_url);
        if ($url === false || empty($url['scheme'])) {
            return null;
        }

        $scheme = strtolower($url['scheme']);
        $user = isset($url['user']) ? rawurldecode($url['user']) : null;
        $pass = isset($url['pass']) ? rawurldecode($url['pass']) : null;
        $host = $url['host'] ?? null;
        $port = $url['port'] ?? null;
        $path = isset($url['path']) ? ltrim($url['path'], '/') : null;

        if (in_array($scheme, ['sqlite', 'sqlite3'], true)) {
            if (!empty($url['host']) && !str_starts_with($path, '/')) {
                $path = '/' . trim($url['host'] . '/' . $path, '/');
            }

            if (!$path) {
                return null;
            }

            return [
                'dsn' => 'sqlite:' . $path,
                'user' => null,
                'pass' => null,
            ];
        }

        if (in_array($scheme, ['postgres', 'postgresql', 'pgsql'], true)) {
            $dsn = 'pgsql:host=' . $host;
            if ($port) {
                $dsn .= ';port=' . $port;
            }
            if ($path) {
                $dsn .= ';dbname=' . $path;
            }
            return [
                'dsn' => $dsn,
                'user' => $user,
                'pass' => $pass,
            ];
        }

        if ($scheme === 'mysql') {
            $dsn = 'mysql:host=' . $host;
            if ($port) {
                $dsn .= ';port=' . $port;
            }
            if ($path) {
                $dsn .= ';dbname=' . $path;
            }
            $dsn .= ';charset=utf8mb4';

            return [
                'dsn' => $dsn,
                'user' => $user,
                'pass' => $pass,
            ];
        }

        return null;
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
