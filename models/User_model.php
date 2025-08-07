<?php
class User_model {
    private $table = 'Tbl_User';
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllUsers() {
        $this->db->query('SELECT id_user, nama_user, level, username FROM ' . $this->table);
        return $this->db->resultSet();
    }

    public function getUserByUsername($username) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE username=:username');
        $this->db->bind('username', $username);
        return $this->db->single();
    }

    public function createUser($data) {
        $query = "INSERT INTO Tbl_User (nama_user, level, username, password) VALUES (:nama_user, :level, :username, :password)";
        
        $this->db->query($query);
        $this->db->bind('nama_user', $data['nama_user']);
        $this->db->bind('level', $data['level']);
        $this->db->bind('username', $data['username']);
        
        // Hash password sebelum disimpan
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $this->db->bind('password', $hashedPassword);

        $this->db->execute();
        return $this->db->rowCount();
    }

    public function updateUser($id, $data) {
        $query = "UPDATE Tbl_User SET nama_user = :nama_user, level = :level, username = :username WHERE id_user = :id_user";
        
        $this->db->query($query);
        $this->db->bind('nama_user', $data['nama_user']);
        $this->db->bind('level', $data['level']);
        $this->db->bind('username', $data['username']);
        $this->db->bind('id_user', $id);

        $this->db->execute();
        return $this->db->rowCount();
    }

    public function deleteUser($id) {
        $query = "DELETE FROM Tbl_User WHERE id_user = :id_user";
        $this->db->query($query);
        $this->db->bind('id_user', $id);

        $this->db->execute();
        return $this->db->rowCount();
    }



    public function updatePassword($id, $newPassword) {
        $query = "UPDATE Tbl_User SET password = :password WHERE id_user = :id_user";
        
        $this->db->query($query);
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->db->bind('password', $hashedPassword);
        $this->db->bind('id_user', $id);

        $this->db->execute();
        return $this->db->rowCount();
    }
}
