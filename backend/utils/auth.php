<?php
class Auth {
    private $conn;
    private $table_name = "admin_users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login($username, $password) {
        $query = "SELECT id, username, password FROM " . $this->table_name . " WHERE username = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row && password_verify($password, $row['password'])) {
            // Generate JWT token
            $token = $this->generateToken($row['id'], $row['username']);
            
            return array(
                "success" => true,
                "token" => $token,
                "user" => array(
                    "id" => $row['id'],
                    "username" => $row['username']
                )
            );
        }

        return array("success" => false, "message" => "Invalid credentials");
    }

    public function generateToken($user_id, $username) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'user_id' => $user_id,
            'username' => $username,
            'exp' => time() + (24 * 60 * 60) // 24 hours
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'your-secret-key', true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public function verifyToken($token) {
        $parts = explode('.', $token);
        if (count($parts) != 3) {
            return false;
        }

        $header = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[0]));
        $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1]));
        $signature = $parts[2];

        $data = json_decode($payload, true);

        if (!$data || !isset($data['exp']) || $data['exp'] < time()) {
            return false;
        }

        return $data;
    }

    public function isAuthenticated() {
        $headers = getallheaders();
        $auth_header = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        
        if (strpos($auth_header, 'Bearer ') === 0) {
            $token = substr($auth_header, 7);
            $payload = $this->verifyToken($token);
            return $payload !== false;
        }

        return false;
    }

    public function getCurrentUser() {
        $headers = getallheaders();
        $auth_header = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        
        if (strpos($auth_header, 'Bearer ') === 0) {
            $token = substr($auth_header, 7);
            $payload = $this->verifyToken($token);
            
            if ($payload !== false) {
                return array(
                    "user_id" => $payload['user_id'],
                    "username" => $payload['username']
                );
            }
        }

        return null;
    }
}
?>
