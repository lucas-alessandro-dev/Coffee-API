<?php
namespace Api\Models;
use Api\Classes\Database;
use Api\Util\UtilClass as Util;

class User {
    private $conn;
    private $db;

    public function __construct() {
        $this->conn = new Database();
    }
    
    public function saveUsers($data): int|string {
        try {
            $this->db = $this->conn->dbConnection();
            $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':name', $data['name']);
            $stmt->bindValue(':email', $data['email']);
            $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return $this->db->lastInsertId();
            }
        } catch (\PDOException $e) {
            return Util::message(500, $e->getMessage());
        }
    }

    public function updateUser($data): string{
        try {
            $this->db = $this->conn->dbConnection();
            if (isset($data['id'])) {
                $sql = "UPDATE users SET ";
                $params = [];
                if (isset($data['name'])) {
                    $sql .= "name = :name";
                    $params[':name'] = $data['name'];
                }
                if (isset($data['email'])) {
                    if (isset($data['name'])) {
                        $sql .= ", ";
                    }
                    $sql .= "email = :email";
                    $params[':email'] = $data['email'];
                }
                if (isset($data['password'])) {
                    if (isset($data['name']) || isset($data['email'])) {
                        $sql .= ", ";
                    }
                    $sql .= "password = :password";
                    $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                }
                $sql .= " WHERE id = :id";
                $params[':id'] = $data['id'];

                $stmt = $this->db->prepare($sql);
                foreach ($params as $param => $value) {
                    $stmt->bindValue($param, $value);
                }
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    Util::message(200, "User updated successfully", 'success');
                } else {
                    Util::message(400, "Nothing was changed");
                }
            }
            Util::message(400, "User not found");
        } catch (\PDOException $e) {    
            return Util::message(500, $e->getMessage());
        }
    }

    public function getAllUsers(): array|string {
        try {
            $this->db = $this->conn->dbConnection();
            $sql = "SELECT id AS user_id, name, email FROM users";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                return $result;
            }
            Util::message(404, "User not found");
        } catch (\PDOException $e) {
            return Util::message(500, $e->getMessage());
        }
    }

    public function getUserById($id): array|string {
        try {
            $this->db = $this->conn->dbConnection();
            $sql = "
                SELECT 
                    users.id AS user_id,
                    users.name,
                    users.email,
                    COUNT(coffee.id_drink_coffee) as drink_counter
                FROM 
                    users
                LEFT JOIN user_drink_coffee coffee  ON  users.id = coffee.id_user
                WHERE users.id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                return $result;
            }
            Util::message(404, "User not found");
        } catch (\PDOException $e) {
            return Util::message(500, $e->getMessage());
        }
    }
    public function checkUserExist ($id) {
        try {
            $this->db = $this->conn->dbConnection();
            $sql = "SELECT * FROM users WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return true;
            }
            return false;
        } catch (\PDOException $e) {
            return Util::message(500, $e->getMessage());
        }
    }
    public function checkNameOrEmailExists($name = null, $email = null, $id = null) {
        try {
            $this->db = $this->conn->dbConnection();
            $sql = "SELECT * FROM users WHERE ";
            $params = [];
            if ($id !== null) {
                $sql .= "id = :id";
                $params[':id'] = $id;
            }
            if ($name !== null) {
                $sql .= "name = :name";
                $params[':name'] = $name;
            }
            if ($email !== null) {
                if ($name !== null) {
                    $sql .= " OR ";
                }
                $sql .= "email = :email";
                $params[':email'] = $email;
            }
            $stmt = $this->db->prepare($sql);
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                Util::message(400, "User already exists. Please verify the 'name' and 'email' fields.");
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            return Util::message(500, $e->getMessage());
        }
    }

    public function deleteUser($id): string{
        try {
            $this->db = $this->conn->dbConnection();
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                Util::message(200, "User deleted successfully", 'success');
            }
            Util::message(400, "User not found");
        } catch (\PDOException $e) {
            return Util::message(500, $e->getMessage());
        }
    }

    public function getUserLogin($data): array|string{
        $this->db = $this->conn->dbConnection();
        $query ="
             SELECT 
                users.id AS user_id,
                users.name,
                users.email,
                users.password,
                COUNT(coffee.id_drink_coffee) as drink_counter
            FROM 
                users
            LEFT JOIN user_drink_coffee coffee  ON  users.id = coffee.id_user
            WHERE users.email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $data['email']);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $user;
        }
        Util::message(400, "Invalid 'e-mail'");
    }

    public function saveTokenUser($id, $token): mixed  {
        try {
            $this->db = $this->conn->dbConnection();
            $sql = "
                INSERT INTO tokens 
                    (token, id_token_user, expiration_time) 
                VALUES 
                    (:token, :id_token_user, :expiration_time)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':token', $token);
            $stmt->bindValue(':id_token_user', $id);
            $expirationTime = date('Y-m-d H:i:s', strtotime('+1 day'));
            $stmt->bindValue(':expiration_time', $expirationTime);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return $stmt;
            }
        } catch (\PDOException $e) {
            return Util::message(500, $e->getMessage());
        }
    }

    public function updateTokenUser($id): mixed {
        try {
            $this->db = $this->conn->dbConnection();
            $sql = "UPDATE tokens SET expiration_time = :expiration_time WHERE id_token_user = :id_token_user";
            $stmt = $this->db->prepare($sql);
            $expirationTime = date('Y-m-d H:i:s', strtotime('+1 day'));
            $stmt->bindValue(':expiration_time', $expirationTime);
            $stmt->bindValue(':id_token_user', $id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return $stmt;
            }
        } catch (\PDOException $e) {
           return Util::message(500, $e->getMessage());
        }
    }

    public function getTokenUser($id): array|string {
        try {
            $this->db = $this->conn->dbConnection();
            $sql = "SELECT * FROM tokens WHERE id_token_user = :id_token_user";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id_token_user', $id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                return $result;
            }
            return false;
        } catch (\PDOException $e) {
            return Util::message(500, $e->getMessage());
        }
    }
}
