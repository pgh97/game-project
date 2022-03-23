<?php

namespace LOGTOOL\Config;

use PDO;
use PDOException;

class DatabaseConfig
{
    private $host = "192.168.147.1";//포워딩한 호스트 IP
    private $db_name = "fishgame_log";
    private $username = "root";
    private $password = "Q1w2e3r4!@";

    public function getConnection(){
        try {
            $conn_str = "mysql:host=".$this->host.";dbname=".$this->db_name;
            $conn = new PDO($conn_str, $this->username, $this->password);
            $conn->exec("set names utf8"); // 설정을 안해주면 한글깨짐.
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch (PDOException $exception){
            echo "Connection error: ".$exception->getMessage();
        }

        return $conn;
    }
}