<?php
declare(strict_types=1);

use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\ResponseEmitter\ResponseEmitter;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate PHP-DI ContainerBuilder, 컨테이너 빌더 생성
$containerBuilder = new ContainerBuilder();

if (false) { // Should be set to true in production
    $containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
}

// Set up settings, 세팅 관련 php (DB 정보 까지)
$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

// Set up dependencies, 의존성 관련 php
$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);

// Set up repositories, DB 관련 저장소
$repositories = require __DIR__ . '/../app/repositories.php';
$repositories($containerBuilder);

// Build PHP-DI Container instance, 컨테이너 인스턴스 생성
$container = $containerBuilder->build();

// Instantiate the app, 앱 실행 컨테이너 코드
AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();

// Register middleware, 미들웨어 등록 관련 php
$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);

// Register routes, 라우터 관련 php 예로 들면, api url 관련된 것들이 모여있는 곳
$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

/** @var SettingsInterface $settings */
$settings = $container->get(SettingsInterface::class);

$displayErrorDetails = $settings->get('displayErrorDetails');
$logError = $settings->get('logError');
$logErrorDetails = $settings->get('logErrorDetails');

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// Create Error Handler, 서버 에러나 썻다운 헨들러 관련된 것들
$responseFactory = $app->getResponseFactory();
$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

// Create Shutdown Handler
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
register_shutdown_function($shutdownHandler);

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Body Parsing Middleware
$app->addBodyParsingMiddleware();

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logError, $logErrorDetails);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);