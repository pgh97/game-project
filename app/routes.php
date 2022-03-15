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
            $auth->post('/info', Actions\Auth\GetAuthAction::class)->add(new JWTAuthMiddleware());
            $auth->post('/info/change', Actions\Auth\ModifyAuthAction::class)->add(new JWTAuthMiddleware());
            $auth->post('/remove', Actions\Auth\DeleteAuthAction::class)->add(new JWTAuthMiddleware());
        });

        $group->group('/user', function (Group $user){
            $user->post('/generate', Actions\User\CreateUserAction::class);
            $user->post('/infos', Actions\User\GetUserListAction::class);
            $user->post('/info', Actions\User\GetUserAction::class);
            $user->post('/info/change', Actions\User\ModifyUserAction::class);
            //$user->post('/level-up', );
            $user->post('/remove', Actions\User\DeleteUserAction::class);
            $user->post('/choice', Actions\User\ChoiceUserAction::class);
            $user->post('/my-weather', Actions\User\GetWeatherInfoAction::class);
            $user->post('/my-weather/change', Actions\User\ModifyWeatherAction::class);
            $user->post('/my-ship', Actions\User\GetUserShipAction::class);
            //$user->post('/fish-dictionary', );
            $user->post('/inventory-items', Actions\User\GetUserInventoryListAction::class);
            $user->post('/inventory-item', Actions\User\GetUserInventoryAction::class);
            //$user->post('/inventory-item/change', Actions\User\ModifyUserInventoryAction::class);
            $user->post('/inventory-item/remove', Actions\User\DeleteUserInventoryAction::class);
            //$user->post('/fishing-items', );
            //$user->post('/fishing-item', );
            $user->post('/fishing-item/change', Actions\User\CreateUserFishingItemAction::class);
            //$user->post('/fishing-item/change', );
            //$user->post('/fishing-item/remove', );
            //$user->post('/gift-boxs', );
            //$user->post('/gift-box', );
            //$user->post('/gift-box/change', );
        })->add(new JWTAuthMiddleware());

        $group->group('/map', function (Group $map){
            $map->post('/areas', Actions\Map\GetMapListAction::class);
            $map->post('/area', Actions\Map\GetMapAction::class);
            $map->post('/leve-port', Actions\Map\MapLevePortAction::class)->add(new JWTAuthMiddleware());
            //$map->post('/enter-port', Actions\Map\MapEnterPortAction::class)->add(new JWTAuthMiddleware());
            $map->post('/ship-durability', Actions\Map\ModifyShipAction::class)->add(new JWTAuthMiddleware());
        });

        $group->group('/fishing', function (Group $fishing){
            $fishing->post('/operate', Actions\Fishing\FishingOperateAction::class);
            $fishing->post('/inventorys', Actions\Fishing\GetFishInventoryListAction::class);
            $fishing->post('/inventory', Actions\Fishing\GetFishInventoryAction::class);
        })->add(new JWTAuthMiddleware());

        $group->group('/auction', function (Group $auction){
            $auction->post('/items', Actions\Auction\GetAuctionListAction::class);
            $auction->post('/item', Actions\Auction\GetAuctionAction::class);
            //$auction->post('/user-item/sell', );
            //$auction->post('/ranking', );
        })->add(new JWTAuthMiddleware());

        $group->group('/upgrade', function (Group $upgrade){
            //$upgrade->post('/fishing-item/preview', );
            //$upgrade->post('/fishing-item', );
            //$upgrade->post('/ship-item/preview', );
            //$upgrade->post('/ship-item', );
        })->add(new JWTAuthMiddleware());

        $group->group('/repair', function (Group $repair){
            //$repair->post('/user', );
            //$repair->post('/item', );
        })->add(new JWTAuthMiddleware());

        $group->group('/quest', function (Group $quest){
            $quest->post('/items', Actions\Quest\GetQuestListAction::class);
            $quest->post('/item', Actions\Quest\GetQuestAction::class);
            //$quest->post('/compensation', );
        })->add(new JWTAuthMiddleware());

        $group->group('/shop', function (Group $shop){
            $shop->post('/items', Actions\Shop\GetShopListAction::class);
            $shop->post('/item', Actions\Shop\GetShopAction::class);
            //$shop->post('/item/buy', )->add(new JWTAuthMiddleware());
            //$shop->post('/user-item/sell', )->add(new JWTAuthMiddleware());
        });
    });
};
