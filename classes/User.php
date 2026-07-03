<?php
/**
 * User Class (OOP)
 * Handles registration, login, profile updates, and administrator CRUD on users.
 */
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $name;
    public $email;
    public $password;
    public $phone;
    public $role;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create a new user account (FR1)
     */
    public function register($name, $email, $password, $phone) {
        // Validation to check if email already exists
        $check_query = "SELECT id FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(":email", $email);
        $check_stmt->execute();
        
        if($check_stmt->rowCount() > 0) {
            return "Email is already registered! Please log in.";
        }

        $query = "INSERT INTO " . $this->table_name . " (name, email, password, phone, role) 
                  VALUES (:name, :email, :password, :phone, 'user')";
        $stmt = $this->conn->prepare($query);

        // Sanitize data inputs to prevent XSS
        $name = htmlspecialchars(strip_tags($name));
        $email = htmlspecialchars(strip_tags($email));
        $phone = htmlspecialchars(strip_tags($phone));
        
        // Secure password hashing
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":phone", $phone);

        if($stmt->execute()) {
            return true;
        }
        return "Failed to register. Please try again.";
    }

    /**
     * User/Admin Login Authentication (FR2 & Admin 1)
     */
    public function login($email, $password) {
        $query = "SELECT id, name, email, password, phone, role FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $email = htmlspecialchars(strip_tags($email));
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            if(password_verify($password, $row['password'])) {
                // Populate class properties
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->email = $row['email'];
                $this->phone = $row['phone'];
                $this->role = $row['role'];
                return $row;
            }
        }
        return false;
    }

    /**
     * Get a list of all existing users (Admin 6)
     */
    public function getAllUsers() {
        $query = "SELECT id, name, email, phone, role, created_at FROM " . $this->table_name . " WHERE role = 'user' ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Fetch single user details by ID
     */
    public function getById($id) {
        $query = "SELECT id, name, email, phone, role, created_at FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Update existing user information (Admin 8)
     */
    public function updateUserInfo($id, $name, $email, $phone) {
        $query = "UPDATE " . $this->table_name . " SET name = :name, email = :email, phone = :phone WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $name = htmlspecialchars(strip_tags($name));
        $email = htmlspecialchars(strip_tags($email));
        $phone = htmlspecialchars(strip_tags($phone));

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    /**
     * Delete an existing user (Admin 7)
     */
    public function deleteUser($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>