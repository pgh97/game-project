<?php

namespace App\config\db;

use PDO;
use PDOException;

// mysqli 방법
//function OpenConn()
//{
//    $servername = "localhost";
//    $username = "root";
//    $password = "Q1w2e3r4!@";
//    $db = "fishgame";
//
//    // Create connection
//    $conn = mysqli_connect($servername, $username, $password, $db);
//    mysqli_query($conn, "set names utf8");
//
//    // Check connection
//    if (!$conn) {
//        die("Connection failed: " . mysqli_connect_error());
//    }
//
//    echo "Connected successfully";
//
//    return $conn;
//}

// pdo 방법
class Database{
    private $host = "127.0.0.1";//"localhost";
    private $db_name = "fishgame";
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
