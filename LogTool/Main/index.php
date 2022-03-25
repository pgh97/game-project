<?php

use LOGTOOL\Util\CheckFileUtil;
use LOGTOOL\Config\DatabaseConfig;

require __DIR__ . '/../vendor/autoload.php';

$checkFile = new CheckFileUtil();
$db = new DatabaseConfig();

$checkFile->listFolderFiles("/game/log/scribe/default_primary");
//print_r($checkFile->getDirs());
//print_r($checkFile->getFiles());

foreach ($checkFile->getFiles() as $fileNm){
    $query = "";
    if (strpos($fileNm, "new_user_log") !== false){
        if ($file = fopen($fileNm, "r")) {
            //echo $fileNm;
            while(($line = fgets($file)) !== false) {
                $json = json_decode($line);
                //echo $line;
                $logDate = date("Ymd", strtotime($json->date));
                if(strtotime($checkFile->getYesterday()) == strtotime($logDate)){
                    $query .= "('".$json->date."', '".$json->last_login_date."', '".$json->channel."', ".$json->user_id.", '".
                        $json->app_id."', '".$json->client_ip."', '".$json->server_ip."', '".$json->guid."'), ";
                }

            }
            fclose($file);
        }
        $query = rtrim($query, ', ');
        echo $query;
    }
}

date_default_timezone_set('Asia/Seoul');

$timeNow = date("Ymd");

$timeNow2 = date("Ymd", strtotime('2022-03-25 13:22:22'));

echo $timeNow;

echo " ";

echo $timeNow2;

if(strtotime($timeNow) == strtotime($timeNow2)){
    echo " dDDD";
}else{
    echo " 3333333 dDDD";
}