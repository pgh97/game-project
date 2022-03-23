<?php
use LOGTOOL\Util\CheckFileUtil;
use LOGTOOL\Config\DatabaseConfig;
use LOGTOOL\Config\LoggerConfig;

require __DIR__ . '/../../vendor/autoload.php';

$checkFile = new CheckFileUtil();
$checkFile->listFolderFiles("/game/log/scribe/default_primary");

// pdo 연결
$db = new DatabaseConfig();
$conn = $db->getConnection();

$logger = new LoggerConfig();

/*foreach ($fileArray as $fileNm){
    if ($file = fopen($fileNm, "r")) {
        while(!feof($file)) {
            $line = fgets($file);
            echo $line;
        }
        fclose($file);
    }
}*/

foreach ($checkFile->getFiles() as $fileNm)
{
    $query = "";
    if (strpos($fileNm, "new_user_log") !== false){
        if ($file = fopen($fileNm, "r")) {
            while(!feof($file)) {
                $line = fgets($file);
                $json = json_decode($line);
                var_dump($json->date);
            }
            fclose($file);
        }


    } elseif (strpos($fileNm, "login_log_") !== false){
        $query = "create table ";
        if (strpos($fileNm, "uruk_game_character_") !== false){
            $query = "";
        } else {
            $query = "";
        }
    } elseif (strpos($fileNm, "withdraw_log") !== false){
        $query = "";
    } elseif (strpos($fileNm, "uruk_game_character_creation") !== false){
        $query = "";
    } elseif (strpos($fileNm, "uruk_game_character_delete") !== false){
        $query = "";
    }
    if(!empty($query)){
        $statement = $conn->prepare($query);
        if ($statement->execute()) {
            $logger->logInfo($fileNm." table insert successfully");
        } else {
            $logger->logInfo($fileNm." table insert fail");
        }
    }else{
        $logger->logInfo("query empty and not insert ".$fileNm);
    }
}