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
            $auth->post('/signup', Actions\Auth\CreateAuthAction::class);                                           //회원가입 API
            $auth->post('/login', Actions\Auth\LoginAuthAction::class);                                             //로그인 API
            $auth->post('/info', Actions\Auth\GetAuthAction::class)->add(new JWTAuthMiddleware());                  //회원정보 조회 API
            $auth->post('/info/change', Actions\Auth\ModifyAuthAction::class)->add(new JWTAuthMiddleware());        //회원정보 수정 API
            $auth->post('/remove', Actions\Auth\DeleteAuthAction::class)->add(new JWTAuthMiddleware());             //회원탈퇴 API
        });

        $group->group('/user', function (Group $user){
            $user->post('/generate', Actions\User\CreateUserAction::class);                                         //캐릭터 등록 API
            $user->post('/infos', Actions\User\GetUserListAction::class);                                           //캐릭터 목록 조회 API
            $user->post('/info', Actions\User\GetUserAction::class);                                                //캐릭터 상세 조회 API
            $user->post('/info/change', Actions\User\ModifyUserAction::class);                                      //캐릭터 정보 수정 API
            $user->post('/remove', Actions\User\DeleteUserAction::class);                                           //캐릭터 삭제 API
            $user->post('/choice', Actions\User\ChoiceUserAction::class);                                           //캐릭터 선택(시작하기) API
            $user->post('/my-weather', Actions\User\GetWeatherInfoAction::class);                                   //캐릭터 날씨 조회 API
            $user->post('/my-weather/change', Actions\User\ModifyWeatherAction::class);                             //캐릭터 날씨 수정 API
            $user->post('/my-ship', Actions\User\GetUserShipAction::class);                                         //캐릭터별 보로롱24호 조회 API
            $user->post('/fish-dictionarys', Actions\User\GetUserFishDictionaryListAction::class);                  //캐릭터 물고기 도감 목록 조회 API
            $user->post('/fish-dictionary', Actions\User\GetUserFishDictionaryAction::class);                       //캐릭터 물고기 도감 상세 조회 API
            $user->post('/inventory-items', Actions\User\GetUserInventoryListAction::class);                        //캐릭터 인벤토리 목록 조회 API
            $user->post('/inventory-item', Actions\User\GetUserInventoryAction::class);                             //캐릭터 인벤토리 상세 조회 API
            $user->post('/inventory-item/remove', Actions\User\DeleteUserInventoryAction::class);                   //캐릭터 인벤토리 삭제 API
            $user->post('/fishing-items', Actions\User\GetUserFishingItemListAction::class);                        //캐릭터 채비 목록 조회 API
            $user->post('/fishing-item', Actions\User\GetUserFishingItemAction::class);                             //캐릭터 채비 상세 조회 API
            $user->post('/fishing-item/save', Actions\User\CreateUserFishingItemAction::class);                     //캐릭터 채비 등록 API
            $user->post('/fishing-item/change', Actions\User\ModifyUserFishingItemAction::class);                   //캐릭터 채비 수정 API
            $user->post('/fishing-item/remove', Actions\User\DeleteUserFishingItemAction::class);                   //캐릭터 채비 삭제 API
            $user->post('/gift-boxs', Actions\User\GetUserGiftBoxListAction::class);                                //선물함(우편함) 목록 조회 API
            $user->post('/gift-box', Actions\User\GetUserGiftBoxAction::class);                                     //선물함(우편함) 상세 조회 API
            $user->post('/gift-box/change', Actions\User\ModifyUserGiftBoxAction::class);                           //선물함 받기 API
            $user->post('/gift-box/remove', Actions\User\DeleteUserGiftBoxAction::class);                           //산물함 삭제 API
        })->add(new JWTAuthMiddleware());   //JWT 미들웨어

        $group->group('/map', function (Group $map){
            $map->post('/areas', Actions\Map\GetMapListAction::class);                                              //지역 목록 조회 API
            $map->post('/area', Actions\Map\GetMapAction::class);                                                   //지역 상세 조회 API
            $map->post('/leve-port', Actions\Map\MapLevePortAction::class)->add(new JWTAuthMiddleware());           //출항하기 API
            $map->post('/enter-port', Actions\Map\MapEnterPortAction::class)->add(new JWTAuthMiddleware());         //입항하기 API
            $map->post('/ship-durability', Actions\Map\ModifyShipAction::class)->add(new JWTAuthMiddleware());      //보로롱24호 내구소 수정 API
        });

        $group->group('/fishing', function (Group $fishing){
            $fishing->post('/operate', Actions\Fishing\FishingOperateAction::class);                                //낚시하기 API
            $fishing->post('/inventorys', Actions\Fishing\GetFishInventoryListAction::class);                       //물고기 인벤토리 목록 조회 API
            $fishing->post('/inventory', Actions\Fishing\GetFishInventoryAction::class);                            //물고기 인벤토리 상세 조회 API
            $fishing->post('/inventory/remove', Actions\Fishing\DeleteUserFishInventoryAction::class);              //물고기 인벤토리 삭제 API
        })->add(new JWTAuthMiddleware());

        $group->group('/auction', function (Group $auction){
            $auction->post('/items', Actions\Auction\GetAuctionListAction::class);                                  //경매 아이템 목록 조회 API
            $auction->post('/item', Actions\Auction\GetAuctionAction::class);                                       //경매 아이템 상세 조회 API
            $auction->post('/user-item/sell', Actions\Auction\SellAuctionAction::class);                            //경매 아이템 판매 API
            $auction->post('/ranking', Actions\Auction\GetAuctionRankAction::class);                                //주간 랭킹 목록 조회 API
        })->add(new JWTAuthMiddleware());

        $group->group('/upgrade', function (Group $upgrade){
            $upgrade->post('/fishing-item', Actions\Upgrade\ModifyUpgradeFishingItemActon::class);                  //낚시 채비 업그레이드 API
            $upgrade->post('/ship-item', Actions\Upgrade\ModifyUpgradeShipItemActon::class);                        //보로롱 24호 업그레이드 API
        })->add(new JWTAuthMiddleware());

        $group->group('/repair', function (Group $repair){
            $repair->post('/user', Actions\Repair\ModifyRepairUserAction::class);                                   //캐릭터 피로도 회복 API
            $repair->post('/item', Actions\Repair\ModifyRepairItemAction::class);                                   //채비, 보로롱24호 수리 API
        })->add(new JWTAuthMiddleware());

        $group->group('/quest', function (Group $quest){
            $quest->post('/items', Actions\Quest\GetQuestListAction::class);                                        //퀘스트 목록 조회 API
            $quest->post('/item', Actions\Quest\GetQuestAction::class);                                             //퀘스트 상세 조회 API
            $quest->post('/user/items', Actions\Quest\GetQuestListAction::class)->add(new JWTAuthMiddleware());     //캐릭터가 달성한 퀘스트 목록 조회 API
            $quest->post('/user/item', Actions\Quest\GetQuestAction::class)->add(new JWTAuthMiddleware());          //캐릭터가 달성한 퀘스트 상세 조회 API
        });

        $group->group('/shop', function (Group $shop){
            $shop->post('/items', Actions\Shop\GetShopListAction::class);                                           //상점 아이템 목록 조회 API
            $shop->post('/item', Actions\Shop\GetShopAction::class);                                                //상점 아이템 상세 조회 API
            $shop->post('/item/buy', Actions\Shop\BuyShopAction::class)->add(new JWTAuthMiddleware());              //상점 구매 API
            $shop->post('/user-item/sell', Actions\Shop\SellShopAction::class)->add(new JWTAuthMiddleware());       //인벤토리 판매 API
        });
    });
};
