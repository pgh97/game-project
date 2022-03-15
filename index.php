<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php'; // vendor가 설치된 경로

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("root Hello world!");
    return $response;
    phpinfo();
});

$app->run();