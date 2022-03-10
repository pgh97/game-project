<?php
declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger' => [
                    'name' => 'uruk-game',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : '/game/public_html/logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                "db" => [
                    'driver' => 'mysql',
                    'host' => $_SERVER['DB_HOST'],
                    'username' => $_SERVER['DB_USER'],
                    'database' => $_SERVER['DB_NAME'],
                    'password' => $_SERVER['DB_PASS'],
                    'port' => $_SERVER['DB_PORT'],
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'flags' => [
                        // Turn off persistent connections
                        PDO::ATTR_PERSISTENT => false,
                        // Enable exceptions
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        // Emulate prepared statements
                        PDO::ATTR_EMULATE_PREPARES => true,
                        // Set default fetch mode to array
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ],
                ],
                'redis' => [
                    'enabled' => $_SERVER['REDIS_ENABLED'],
                    'url' => $_SERVER['REDIS_URL'],
                    'pass' => $_SERVER['REDIS_PASS'],
                ],
                'app' => [
                    'domain' => $_SERVER['APP_DOMAIN'],
                    'secret' => $_SERVER['SECRET_KEY'],
                ],
            ]);
        }
    ]);
};