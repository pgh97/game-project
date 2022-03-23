<?php

namespace LOGTOOL\Config;

use Monolog\Registry;
require __DIR__ . '/Bootstrap.php';

class LoggerConfig
{
    public function logInfo($message){
        $logger = Registry::getInstance('CrontabLog');
        $logger->info($message);
    }
}