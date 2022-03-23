<?php

use LOGTOOL\Util\CheckFileUtil;
use LOGTOOL\Config\DatabaseConfig;

require __DIR__ . '/../vendor/autoload.php';

$checkFile = new CheckFileUtil();
$db = new DatabaseConfig();

$checkFile->listFolderFiles("/game/log/scribe/default_primary");
//print_r($checkFile->getDirs());

foreach ($checkFile->getFiles() as $fileNm){
    if ($file = fopen($fileNm, "r")) {
        while(!feof($file)) {
            $line = fgets($file);
            $ob = json_decode($line);
            var_dump($ob->date);
        }
        fclose($file);
    }
}
