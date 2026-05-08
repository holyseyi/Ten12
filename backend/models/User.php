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
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function verifyPassword($username, $password) {
        $user = $this->findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
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