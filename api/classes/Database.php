<?php
namespace api\Classes;
require_once 'config.php';

class Database{
    private $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        try{
            $this->conn = new \PDO(DBDRIVE.':host='.DBHOST.'; port='.DBPORT.'; dbname='.DBNAME, DBUSER, DBPASSWORD);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch(\PDOException $e){
            exit("Connection error: ".$e->getMessage());
        }
    }

    public function dbConnection() {
        return $this->conn;
    }
}

