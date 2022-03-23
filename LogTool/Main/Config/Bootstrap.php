<?php

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;
use Monolog\Logger;
use Monolog\Registry;
use LOGTOOL\Util\CheckFileUtil;

$checkFile = new CheckFileUtil();

$logger = new Logger('LogTool');

$processor = new UidProcessor(20);
$logger->pushProcessor($processor);

$handler = new StreamHandler($_SERVER['DOCUMENT_ROOT'].'/LogTool/logs/app_'.$checkFile->getCurrentDay().".log", Logger::DEBUG);
//$formatter = new LineFormatter('[%datetime%] [%level_name%] %message%', 'Y-m-d H:i:s');
//$handler->setFormatter($formatter);

$logger->pushHandler($handler);
Registry::addLogger($logger,'CrontabLog');