<?php
class User_model {
    private $conn;
    private $table_name = "Tbl_User";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllUsers() {
        $query = "SELECT id_user, nama_user, level, username FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserByUsername($username) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createUser($data) {
        $query = "INSERT INTO " . $this->table_name . " SET nama_user=:nama_user, level=:level, username=:username, password=:password";
        $stmt = $this->conn->prepare($query);

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt->bindParam(":nama_user", $data['nama_user']);
        $stmt->bindParam(":level", $data['level']);
        $stmt->bindParam(":username", $data['username']);
        $stmt->bindParam(":password", $hashedPassword);

        $stmt->execute();
        return $stmt->rowCount();
    }

    public function updateUser($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET nama_user = :nama_user, level = :level, username = :username WHERE id_user = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nama_user', $data['nama_user']);
        $stmt->bindParam(':level', $data['level']);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
        return $stmt->rowCount();
    }

    public function deleteUser($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_user = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount();
    }
    
    public function updatePassword($id, $newPassword) {
        $query = "UPDATE " . $this->table_name . " SET password = :password WHERE id_user = :id";
        $stmt = $this->conn->prepare($query);
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $id);
        
        $stmt->execute();
        return $stmt->rowCount();
    }
}
