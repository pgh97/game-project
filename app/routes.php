<?php
declare(strict_types=1);


use App\Application\Actions;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->get('/', Actions\HomeAction::class)->setName('home');

    $app->get('/fishs', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM fish_info_data");
        $sth->execute();
        $data = $sth->fetchAll(PDO::FETCH_ASSOC);
        $payload = json_encode($data);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->group('/api/v1', function () use ($app): void{
        $app->group('/auth', function (Group $group){
            $group->get('/signup', ViewUserAction::class);
        });
    });
};
