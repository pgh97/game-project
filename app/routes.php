<?php
declare(strict_types=1);

use App\Application\Actions;
use App\Application\Middleware\JWTAuthMiddleware;
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

    $app->group('/api/v1', function (Group $group){
        $group->group('/auth', function (Group $auth){
            $auth->post('/signup', Actions\Auth\CreateAuthAction::class);
            $auth->post('/login', Actions\Auth\LoginAuthAction::class);
        });

        $group->group('/user', function (Group $user){
            $user->post('/generate', Actions\User\CreateUserAction::class);
            $user->post('/infos', Actions\User\GetUserAction::class);
        })->add(new JWTAuthMiddleware());
    });
};
