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

//log 선언
$logger = new LoggerConfig();

foreach ($checkFile->getFiles() as $fileNm)
{
    $query = "";
    if (strpos($fileNm, "uruk_game_character_") !== false){
        if (strpos($fileNm, "money_log") !== false){
            $query = "INSERT INTO uruk_game_character_money_log_".$checkFile->getYesterday()." 
                (date, dateTime, channel_uid, game, server_id, account_id, account_level, character_id
                , character_type_id, character_level, character_money_id, character_money_item_id, character_money_item_type
                , character_money_type, character_money_price, app_id, client_ip, server_ip, channel, company, guid)  
                VALUES ";
            if ($file = fopen($fileNm, "r")) {
                while(($line = fgets($file)) !== false) {
                    $json = json_decode($line);
                    $query .= "('".$json->date."', '".$json->dateTime."', '".$json->channel_uid."', '".$json->game."', '".
                        $json->server_id."', ".$json->account_id.", ".$json->account_level.", ".$json->character_id.", ".
                        $json->character_type_id.", ".$json->character_level.", ".$json->character_money_id.", ".$json->character_money_item_id.
                        ", ".$json->character_money_item_type.", ".$json->character_money_type.", ".$json->character_money_price.
                        ", '".$json->app_id."', '".$json->client_ip."', '".
                        $json->server_ip."', '".$json->channel."', '".$json->company."', '".$json->guid."'), ";
                }
                fclose($file);
            }
        } elseif (strpos($fileNm, "auction_log") !== false){
            $query = "INSERT INTO uruk_game_character_auction_log_".$checkFile->getYesterday()." 
                (date, dateTime, channel_uid, game, server_id, account_id, account_level, character_id
                , character_type_id, character_level, character_auction_item_id, character_auction_price
                , character_auction_profit_price, character_auction_count, app_id, client_ip, server_ip, channel, company, guid)  
                VALUES ";
            if ($file = fopen($fileNm, "r")) {
                while(($line = fgets($file)) !== false) {
                    $json = json_decode($line);
                    $query .= "('".$json->date."', '".$json->dateTime."', '".$json->channel_uid."', '".$json->game."', '".
                        $json->server_id."', ".$json->account_id.", ".$json->account_level.", ".$json->character_id.", ".
                        $json->character_type_id.", ".$json->character_level.", ".$json->character_auction_item_id.", ".
                        $json->character_auction_price.", ".$json->character_auction_profit_price.", ".$json->character_auction_count.
                        ", '".$json->app_id."', '".$json->client_ip."', '".
                        $json->server_ip."', '".$json->channel."', '".$json->company."', '".$json->guid."'), ";
                }
                fclose($file);
            }
        } elseif (strpos($fileNm, "fishing_log") !== false){
            $query = "INSERT INTO uruk_game_character_fishing_log_".$checkFile->getYesterday()." 
                (date, dateTime, channel_uid, game, server_id, account_id, account_level, character_id
                , character_type_id, character_level, character_catch_id, character_catch_type
                , app_id, client_ip, server_ip, channel, company, guid)  
                VALUES ";
            if ($file = fopen($fileNm, "r")) {
                while(($line = fgets($file)) !== false) {
                    $json = json_decode($line);
                    $query .= "('".$json->date."', '".$json->dateTime."', '".$json->channel_uid."', '".$json->game."', '".
                        $json->server_id."', ".$json->account_id.", ".$json->account_level.", ".$json->character_id.", ".
                        $json->character_type_id.", ".$json->character_level.", ".$json->character_catch_id.", ".
                        $json->character_catch_type.", '".$json->app_id."', '".$json->client_ip."', '".
                        $json->server_ip."', '".$json->channel."', '".$json->company."', '".$json->guid."'), ";
                }
                fclose($file);
            }
        } elseif (strpos($fileNm, "repair_log") !== false){
            $query = "INSERT INTO uruk_game_character_repair_log_".$checkFile->getYesterday()." 
                (date, dateTime, channel_uid, game, server_id, account_id, account_level, character_id
                , character_type_id, character_level, character_repair_id, character_repair_price, character_repair_durability
                , character_repair_sum, app_id, client_ip, server_ip, channel, company, guid)  
                VALUES ";
            if ($file = fopen($fileNm, "r")) {
                while(($line = fgets($file)) !== false) {
                    $json = json_decode($line);
                    $query .= "('".$json->date."', '".$json->dateTime."', '".$json->channel_uid."', '".$json->game."', '".
                        $json->server_id."', ".$json->account_id.", ".$json->account_level.", ".$json->character_id.", ".
                        $json->character_type_id.", ".$json->character_level.", ".$json->character_repair_id.", ".
                        $json->character_repair_price.", ".$json->character_repair_durability.", ".$json->character_repair_sum.
                        ", '".$json->app_id."', '".$json->client_ip."', '".
                        $json->server_ip."', '".$json->channel."', '".$json->company."', '".$json->guid."'), ";
                }
                fclose($file);
            }
        } elseif (strpos($fileNm, "upgrade_log") !== false){
            $query = "INSERT INTO uruk_game_character_upgrade_log_".$checkFile->getYesterday()." 
                (date, dateTime, channel_uid, game, server_id, account_id, account_level, character_id
                , character_type_id, character_level, character_upgrade_id, character_upgrade_level, character_upgrade_price
                , character_upgrade_type, app_id, client_ip, server_ip, channel, company, guid)  
                VALUES ";
            if ($file = fopen($fileNm, "r")) {
                while(($line = fgets($file)) !== false) {
                    $json = json_decode($line);
                    $query .= "('".$json->date."', '".$json->dateTime."', '".$json->channel_uid."', '".$json->game."', '".
                        $json->server_id."', ".$json->account_id.", ".$json->account_level.", ".$json->character_id.", ".
                        $json->character_type_id.", ".$json->character_level.", ".$json->character_upgrade_id.", ".$json->character_upgrade_level.
                        ", ".$json->character_upgrade_price.", ".$json->character_upgrade_type.", '".$json->app_id."', '".$json->client_ip."', '".
                        $json->server_ip."', '".$json->channel."', '".$json->company."', '".$json->guid."'), ";
                }
                fclose($file);
            }
        } else {
            if (strpos($fileNm, "creation_log_") !== false){
                $query = "INSERT INTO uruk_game_character_creation_log_".$checkFile->getYesterday()." 
                (date, dateTime, channel_uid, game, server_id, account_id, account_level, character_id
                , character_type_id, character_level, app_id, client_ip, server_ip, channel, company, guid)  
                VALUES ";
            } elseif (strpos($fileNm, "login_log_") !== false){
                $query = "INSERT INTO uruk_game_character_login_log_".$checkFile->getYesterday()." 
                (date, dateTime, channel_uid, game, server_id, account_id, account_level, character_id
                , character_type_id, character_level, app_id, client_ip, server_ip, channel, company, guid)  
                VALUES ";
            } else {
                $query = "INSERT INTO uruk_game_character_delete_log_".$checkFile->getYesterday()." 
                (date, dateTime, channel_uid, game, server_id, account_id, account_level, character_id
                , character_type_id, character_level, app_id, client_ip, server_ip, channel, company, guid)  
                VALUES ";
            }
            if ($file = fopen($fileNm, "r")) {
                while(($line = fgets($file)) !== false) {
                    $json = json_decode($line);
                    $query .= "('".$json->date."', '".$json->dateTime."', '".$json->channel_uid."', '".$json->game."', '".
                        $json->server_id."', ".$json->account_id.", ".$json->account_level.", ".$json->character_id.", ".
                        $json->character_type_id.", ".$json->character_level.", '".$json->app_id."', '".$json->client_ip."', '".
                        $json->server_ip."', '".$json->channel."', '".$json->company."', '".$json->guid."'), ";
                }
                fclose($file);
            }
        }
        $query = rtrim($query, ', ');
    } else if (strpos($fileNm, "new_user_log") !== false){
        $query = "INSERT INTO new_user_log_".$checkFile->getYesterday()." 
            (date, last_login_date, channel, user_id, app_id, client_ip, server_ip, guid)  
            VALUES ";
        if ($file = fopen($fileNm, "r")) {
            while(($line = fgets($file)) !== false) {
                $json = json_decode($line);
                $query .= "('".$json->date."', '".$json->last_login_date."', '".$json->channel."', ".$json->user_id.", '".
                    $json->app_id."', '".$json->client_ip."', '".$json->server_ip."', '".$json->guid."'), ";
            }
            fclose($file);
        }
        $query = rtrim($query, ', ');
    } elseif (strpos($fileNm, "login_log_") !== false){
        $query = "INSERT INTO login_log_".$checkFile->getYesterday()." 
                (date, last_login_date, channel, user_id, app_id, client_ip, server_ip, level, guid)  
            VALUES ";
        if ($file = fopen($fileNm, "r")) {
            while(($line = fgets($file)) !== false) {
                $json = json_decode($line);
                $query .= "('".$json->date."', '".$json->last_login_date."', '".$json->channel."', ".$json->user_id.", '".
                    $json->app_id."', '".$json->client_ip."', '".$json->server_ip."', ".$json->level.", '".$json->guid."'), ";
            }
            fclose($file);
        }
        $query = rtrim($query, ', ');
    } elseif (strpos($fileNm, "withdraw_log") !== false){
        $query = "INSERT INTO withdraw_log_".$checkFile->getYesterday()." 
                (date, channel, user_id, app_id, client_ip, server_ip, level)  
            VALUES ";
        if ($file = fopen($fileNm, "r")) {
            while(($line = fgets($file)) !== false) {
                $json = json_decode($line);
                $query .= "('".$json->date."', '".$json->channel."', ".$json->user_id.", '".
                    $json->app_id."', '".$json->client_ip."', '".$json->server_ip."', ".$json->level."), ";
            }
            fclose($file);
        }
        $query = rtrim($query, ', ');
    }

    if(!empty($query)){
        $statement = $conn->prepare($query);
        if ($statement->execute()) {
            $logger->logInfo($fileNm." insert successfully");
        } else {
            $logger->logInfo($fileNm." insert fail");
        }
    }else{
        $logger->logInfo("query empty and not insert ".$fileNm);
    }
}