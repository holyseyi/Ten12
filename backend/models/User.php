<?php
class User {
    private $conn;
    private $table_name = "admin_users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($username, $password, $email, $role = 'user') {
        $query = "INSERT INTO " . $this->table_name . " SET username=:username, password=:password, email=:email, role=:role";
        $stmt = $this->conn->prepare($query);

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":role", $role);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function findByUsername($username) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE username = ?";
            error_log("findByUsername: Executing query for user: $username");
            
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                error_log("findByUsername: Failed to prepare statement: " . print_r($this->conn->errorInfo(), true));
                return false;
            }
            
            $result = $stmt->execute([$username]);
            if (!$result) {
                error_log("findByUsername: Query execution failed: " . print_r($stmt->errorInfo(), true));
                return false;
            }
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                error_log("findByUsername: User found - ID: " . $user['id']);
            } else {
                error_log("findByUsername: No user found with username: $username");
            }
            return $user;
        } catch (Exception $e) {
            error_log("findByUsername: Exception occurred: " . $e->getMessage());
            return false;
        }
    }

    public function findById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function verifyPassword($username, $password) {
        try {
            error_log("verifyPassword: Looking up user: $username");
            
            $user = $this->findByUsername($username);
            
            if (!$user) {
                error_log("verifyPassword: User not found in database: $username");
                return false;
            }
            
            error_log("verifyPassword: User found, verifying password");
            
            if (!isset($user['password'])) {
                error_log("verifyPassword: ERROR - User record has no password field!");
                return false;
            }
            
            $password_match = password_verify($password, $user['password']);
            
            if ($password_match) {
                error_log("verifyPassword: SUCCESS - Password verified for user: $username");
                return $user;
            } else {
                error_log("verifyPassword: FAILED - Password does not match for user: $username");
                error_log("verifyPassword: Hash from DB: " . substr($user['password'], 0, 20) . "...");
                return false;
            }
        } catch (Exception $e) {
            error_log("verifyPassword: Exception occurred: " . $e->getMessage());
            return false;
        }
    }

    public function getAll() {
        $query = "SELECT id, username, email, role, created_at FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET username=:username, email=:email, role=:role WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":username", $data['username']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":role", $data['role']);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    public function updatePassword($id, $password) {
        $query = "UPDATE " . $this->table_name . " SET password=:password WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>