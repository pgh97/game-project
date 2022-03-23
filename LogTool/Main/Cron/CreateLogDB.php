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

foreach ($checkFile->getDirs() as $dir)
{
    $query = "SHOW TABLES LIKE '".$dir."'";
    $statement = $conn->prepare($query);
    $statement->execute();
    if ($statement->rowCount() == 0){
        $query = "";
        if (strpos($dir, "new_user_log") !== false){
            $query = "create table ";
            $query .= $dir." (
               new_user_log_id bigint not null auto_increment primary key,
               date	varchar(50) not null,
               last_login_date varchar(50) not null,
               channel varchar(3) not null,
               user_id bigint not null,
               app_id varchar(200) not null,
               client_ip varchar(32) not null,
               server_ip varchar(32) not null,
               guid varchar(64) null
            )";
        } elseif (strpos($dir, "login_log_") !== false){
            $query = "create table ";
            if (strpos($dir, "uruk_game_character_") !== false){
                $query .= $dir." (
                   character_login_log_id bigint not null auto_increment primary key,
                   date	varchar(50) not null,
                   dateTime varchar(50) not null,
                   channel_uid varchar(64) not null,
                   game varchar(50) not null,
                   server_id varchar(64) not null,
                   account_id bigint not null,
                   account_level int not null,
                   character_id bigint not null,
                   character_type_id int not null,
                   character_level int not null,
                   app_id varchar(200) not null,
                   client_ip varchar(32) not null,
                   server_ip varchar(32) not null,   
                   channel varchar(3) not null,
                   company varchar(3) not null,
                   guid varchar(64) null
                )";
            } else {
                $query .= $dir." (
                   login_log_id bigint not null auto_increment primary key,
                   date	varchar(50) not null,
                   last_login_date varchar(50) not null,
                   channel varchar(3) not null,
                   user_id bigint not null,
                   app_id varchar(200) not null,
                   client_ip varchar(32) not null,
                   server_ip varchar(32) not null,
                   level int not null,
                   guid varchar(64) null
                )";
            }
        } elseif (strpos($dir, "withdraw_log") !== false){
            $query = "create table ";
            $query .= $dir." (
                   withdraw_log_id bigint not null auto_increment primary key,
                   date	varchar(50) not null,
                   channel varchar(3) not null,
                   user_id bigint not null,
                   app_id varchar(200) not null,
                   client_ip varchar(32) not null,
                   server_ip varchar(32) not null,
                   level int not null
                )";
        } elseif (strpos($dir, "uruk_game_character_creation") !== false){
            $query = "create table ";
            $query .= $dir." (
                   creation_log_id bigint not null auto_increment primary key,
                   date	varchar(50) not null,
                   dateTime varchar(50) not null,
                   channel_uid varchar(64) not null,
                   game varchar(50) not null,
                   server_id varchar(64) not null,
                   account_id bigint not null,
                   account_level int not null,
                   character_id bigint not null,
                   character_type_id int not null,
                   character_level int not null,
                   app_id varchar(200) not null,
                   client_ip varchar(32) not null,
                   server_ip varchar(32) not null,   
                   channel varchar(3) not null,
                   company varchar(3) not null,
                   guid varchar(64) null
                )";
        } elseif (strpos($dir, "uruk_game_character_delete") !== false){
            $query = "create table ";
            $query .= $dir." (
                   delete_log_id bigint not null auto_increment primary key,
                   date	varchar(50) not null,
                   dateTime varchar(50) not null,
                   channel_uid varchar(64) not null,
                   game varchar(50) not null,
                   server_id varchar(64) not null,
                   account_id bigint not null,
                   account_level int not null,
                   character_id bigint not null,
                   character_type_id int not null,
                   character_level int not null,
                   app_id varchar(200) not null,
                   client_ip varchar(32) not null,
                   server_ip varchar(32) not null,   
                   channel varchar(3) not null,
                   company varchar(3) not null,
                   guid varchar(64) null
                )";
        }
        if(!empty($query)){
            $statement = $conn->prepare($query);
            if ($statement->execute()) {
                $logger->logInfo($dir." table create successfully");
            } else {
                $logger->logInfo($dir." table create fail");
            }
        }else{
            $logger->logInfo("query empty and not create ".$dir);
        }
    } else {
        $logger->logInfo($dir." table exists");
    }
}