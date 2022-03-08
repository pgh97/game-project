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
            $user->post('/infos', Actions\User\GetUserListAction::class);
            $user->post('/info', Actions\User\GetUserAction::class);
            $user->post('/info/change', Actions\User\ModifyUserAction::class);
            //$user->post('/level-up', );
            $user->post('/my-weather', Actions\User\GetWeatherInfoAction::class);
            //$user->post('/my-weather/change', );
            //$user->post('/my-ship', );
            //$user->post('/fish-dictionary', );
            //$user->post('/inventory-items', );
            //$user->post('/inventory-item', );
            //$user->post('/inventory-item/change', );
            //$user->post('/inventory-item/remove', );
            //$user->post('/fishing-items', );
            //$user->post('/fishing-item', );
            //$user->post('/fishing-item/change', );
            //$user->post('/fishing-item/remove', );
            //$user->post('/gift-boxs', );
            //$user->post('/gift-box', );
            //$user->post('/gift-box/change', );
        })->add(new JWTAuthMiddleware());

        $group->group('/map', function (Group $map){
            //$map->post('/areas', );
            //$map->post('/area/{mapCode}', );
            //$map->post('/leve-port', );
            //$map->post('/enter-port', );
        })->add(new JWTAuthMiddleware());

        $group->group('/fishing', function (Group $fishing){
            //$fishing->post('/operate', );
            //$fishing->post('/inventorys', );
            //$fishing->post('/inventory', );
        })->add(new JWTAuthMiddleware());

        $group->group('/auction', function (Group $auction){
            //$auction->post('/items', );
            //$auction->post('/item', );
            //$auction->post('/user-item/sell', );
            //$auction->post('/ranking', );
        })->add(new JWTAuthMiddleware());

        $group->group('/upgrade', function (Group $user){
        })->add(new JWTAuthMiddleware());

        $group->group('/repair', function (Group $user){
        })->add(new JWTAuthMiddleware());

        $group->group('/quest', function (Group $user){
        })->add(new JWTAuthMiddleware());

        $group->group('/shop', function (Group $user){
        })->add(new JWTAuthMiddleware());

        $group->group('/fishing', function (Group $user){
        })->add(new JWTAuthMiddleware());
    });
};
