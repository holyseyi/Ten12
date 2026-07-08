<?php
class Project {
    private $conn;
    private $table_name = "projects";

    public $id;
    public $title;
    public $description;
    public $content;
    public $thumbnail;
    public $images;
    public $category;
    public $tags;
    public $live_url;
    public $github_url;
    public $published;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (title, description, content, thumbnail, images, category, tags, live_url, github_url, published)
                VALUES (:title, :description, :content, :thumbnail, :images, :category, :tags, :live_url, :github_url, :published)";

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->content = htmlspecialchars(strip_tags($this->content));
        $this->thumbnail = htmlspecialchars(strip_tags($this->thumbnail));
        $this->images = htmlspecialchars(strip_tags($this->images));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->tags = htmlspecialchars(strip_tags($this->tags));
        $this->live_url = htmlspecialchars(strip_tags($this->live_url));
        $this->github_url = htmlspecialchars(strip_tags($this->github_url));
        $this->published = htmlspecialchars(strip_tags($this->published));

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":thumbnail", $this->thumbnail);
        $stmt->bindParam(":images", $this->images);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":tags", $this->tags);
        $stmt->bindParam(":live_url", $this->live_url);
        $stmt->bindParam(":github_url", $this->github_url);
        $stmt->bindParam(":published", $this->published);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function readAll($published_only = true) {
        $query = "SELECT * FROM " . $this->table_name;
        
        if ($published_only) {
            $query .= " WHERE published = 1";
        }
        
        $query .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->title = $row['title'];
        $this->description = $row['description'];
        $this->content = $row['content'];
        $this->thumbnail = $row['thumbnail'];
        $this->images = $row['images'];
        $this->category = $row['category'];
        $this->tags = $row['tags'];
        $this->live_url = $row['live_url'];
        $this->github_url = $row['github_url'];
        $this->published = $row['published'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];

        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET title=:title, description=:description, content=:content, 
                    thumbnail=:thumbnail, images=:images, category=:category, 
                    tags=:tags, live_url=:live_url, github_url=:github_url, 
                    published=:published
                WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->content = htmlspecialchars(strip_tags($this->content));
        $this->thumbnail = htmlspecialchars(strip_tags($this->thumbnail));
        $this->images = htmlspecialchars(strip_tags($this->images));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->tags = htmlspecialchars(strip_tags($this->tags));
        $this->live_url = htmlspecialchars(strip_tags($this->live_url));
        $this->github_url = htmlspecialchars(strip_tags($this->github_url));
        $this->published = htmlspecialchars(strip_tags($this->published));

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":thumbnail", $this->thumbnail);
        $stmt->bindParam(":images", $this->images);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":tags", $this->tags);
        $stmt->bindParam(":live_url", $this->live_url);
        $stmt->bindParam(":github_url", $this->github_url);
        $stmt->bindParam(":published", $this->published);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function search($keywords) {
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE title LIKE ? OR description LIKE ? OR content LIKE ? OR tags LIKE ?
                  AND published = 1
                  ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);

        $keywords = "%{$keywords}%";

        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        $stmt->bindParam(4, $keywords);

        $stmt->execute();

        return $stmt;
    }
}
?>
