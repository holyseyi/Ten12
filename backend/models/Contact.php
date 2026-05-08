<?php
class Contact {
    private $conn;
    private $table_name = "contacts";

    public $id;
    public $name;
    public $email;
    public $message;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET name=:name, email=:email, message=:message";

        $stmt = $this->conn->prepare($query);

        // Sanitize input data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = filter_var($this->email, FILTER_SANITIZE_EMAIL);
        $this->message = htmlspecialchars(strip_tags($this->message));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":message", $this->message);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}
?>
