<?php
declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use App\Domain\Common\Service\RedisService;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
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
        }
    ]);
};