<?php

use LOGTOOL\Util\CheckFileUtil;
use LOGTOOL\Config\DatabaseConfig;

require __DIR__ . '/../vendor/autoload.php';

$checkFile = new CheckFileUtil();
$db = new DatabaseConfig();

$checkFile->listFolderFiles("/game/log/scribe/default_primary");
//print_r($checkFile->getFiles());

foreach ($checkFile->getFiles() as $fileNm){
    $query = "";
    if (strpos($fileNm, "new_user_log") !== false){
        if ($file = fopen($fileNm, "r")) {
            //echo $fileNm;
            while(($line = fgets($file)) !== false) {
                $json = json_decode($line);
                //echo $line;
                $query .= "('".$json->date."', '".$json->last_login_date."', '".$json->channel."', ".$json->user_id.", '".
                    $json->app_id."', '".$json->client_ip."', '".$json->server_ip."', '".$json->guid."'), ";
            }
            fclose($file);
        }
        $query = rtrim($query, ', ');
        echo $query;
    }
}
