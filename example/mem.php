<?php
header("Content-Type: text/html; charset=UTF-8");
$memc = new Memcached();
$memc->addServer("localhost", "11211");     //memcached 서버접속 세팅
$userName = $_GET["userName"];

if(!empty($userName)){
    $mem_userName = $memc->get($userName);  // memcached에서 userName을 통해 유저 정보를 가져옴
    if($mem_userName){  // 캐싱되어있을 경우
        echo "memcached에서 가져온 userName: " . $mem_userName;
    }else{  // 없을 경우
        $dbh = new PDO("mysql:host=localhost;dbname=game_test","root","Fhekwn6471!@"
            ,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

        $stmt = $dbh->prepare("SELECT user_name FROM user_info WHERE user_name = :user_name");
        $stmt->bindParam(":user_name", $userName, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();
        echo "MySQL에서 가져온 userName: " . $result["user_name"];
        $memc->set($userName, $result["user_name"]);
    }
}
?>