<?php
declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TFramedTransport;
use Thrift\Transport\TSocketPool;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor(20);
            $_SERVER['GUID'] = $processor->getUid();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);
            return $logger;
        },

        PDO::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $dbSettings = $settings->get('db');

            $host = $dbSettings['host'];
            $dbname = $dbSettings['database'];
            $username = $dbSettings['username'];
            $password = $dbSettings['password'];
            $port = $dbSettings['port'];
            $charset = $dbSettings['charset'];
            $flags = $dbSettings['flags'];
            $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return $pdo;
        },

        Predis\Client::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $redis = $settings->get('redis');
            $client = new Predis\Client($redis['url']);
            $client->auth($redis['pass']);
            return $client;
        },

        \scribeClient::class => function (ContainerInterface $c){
            $settings = $c->get(SettingsInterface::class);
            $scribe = $settings->get('scribe');
            $scribe_servers = array($scribe['host']);
            $scribe_ports = array($scribe['port']);
            $socket = new TSocketPool($scribe_servers, $scribe_ports);
            $socket->setDebug(0);
            $socket->setSendTimeout(1000);
            $socket->setRecvTimeout(2500);
            $socket->setNumRetries(1);
            $socket->setRandomize(false);
            $socket->setAlwaysTryLast(true);
            $transport = new TFramedTransport($socket);
            $protocol = new TBinaryProtocol($transport);
            $transport->open();
            return new scribeClient($protocol);
        },
    ]);
};