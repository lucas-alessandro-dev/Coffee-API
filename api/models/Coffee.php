<?php
namespace Api\Models;

use Api\Classes\Database;
use Api\Util\UtilClass as Util;

class Coffee {

    private $conn;
    private $db;

    public function __construct() {
        $this->conn = new Database();    
    }

    public function addDrink($id): int|string {
        try {
            $this->db = $this->conn->dbConnection();
            $sql = "INSERT INTO user_drink_coffee (id_user) VALUES (:id_user)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id_user', $id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return $this->db->lastInsertId();
            }
        } catch (\PDOException $e) {
            return Util::message(500, $e->getMessage());
        }
    }

    public function getRankingUsersPerDay($date) {
        try {
            $this->db = $this->conn->dbConnection();
            $sql = "
                SELECT 
                    users.name,
                    COUNT(coffee.id_drink_coffee) as drink_counter
                FROM 
                    user_drink_coffee coffee 
                LEFT JOIN users ON coffee.id_user = users.id 
                WHERE DATE(coffee.created_at) =  STR_TO_DATE(:date, '%d/%m/%Y')
                GROUP BY 
                    users.name
                ORDER BY 
                    drink_counter DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':date', $date);
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

    public function getUserRecordHistory($userId) {
        try {
            $this->db = $this->conn->dbConnection();
           $sql ="
                SELECT 
                    DATE(coffee.created_at) as date,
                    COUNT(coffee.id_drink_coffee) as drink_counter
                FROM 
                    user_drink_coffee coffee 
                WHERE coffee.id_user = :userId
                GROUP BY 
                    DATE(coffee.created_at)
                ORDER BY 
                    date DESC
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
            $stmt->execute();
            $history = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $history;
        } catch (\PDOException $e) {
            return Util::message(500, $e->getMessage());
        }
    }

    public function rankingUserDateRange($date_start, $date_end) {
        try {
            $this->db = $this->conn->dbConnection();
            $sql = "
                SELECT 
                    users.name,
                    COUNT(coffee.id_drink_coffee) as drink_counter
                FROM 
                    user_drink_coffee coffee 
                LEFT JOIN users ON coffee.id_user = users.id 
                WHERE DATE(coffee.created_at) BETWEEN STR_TO_DATE(:date_start, '%d/%m/%Y') AND STR_TO_DATE(:date_end, '%d/%m/%Y')
                GROUP BY 
                    users.name
                ORDER BY 
                    drink_counter DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':date_start', $date_start);
            $stmt->bindValue(':date_end', $date_end);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                return $result;
            }
            Util::message(404, "there is no data in this filter range");
        } catch (\PDOException $e) {
            return Util::message(500, $e->getMessage());
        }
    }

    public function getRankingUsersLastDays($days) {
        try {
            $this->db = $this->conn->dbConnection();
            $sql = "
                SELECT 
                    users.id AS user_id,
                    users.name,
                    users.email,
                    COUNT(coffee.id_drink_coffee) as drink_counter
                FROM 
                    user_drink_coffee coffee 
                LEFT JOIN users ON coffee.id_user = users.id 
                WHERE coffee.created_at >= DATE(NOW()) - INTERVAL :days DAY
                GROUP BY 
                    users.id,
                    users.name,
                    users.email
                ORDER BY 
                    drink_counter DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':days', $days, \PDO::PARAM_INT);
            $stmt->execute();
            $ranking = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $ranking;
        } catch (\PDOException $e) {
            return Util::message(500, $e->getMessage());
        }
    }
}