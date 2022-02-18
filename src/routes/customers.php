<?php
namespace App\routes\customer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../vendor/autoload.php';
$app = new \Slim\App;

$app->get('/api/customers', function (Request $request, Response $response){
   echo 'CUSTOMERS';
});