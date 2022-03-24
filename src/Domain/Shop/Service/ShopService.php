<?php

namespace App\Domain\Shop\Service;

use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\Common\Service\ScribeService;
use App\Domain\Shop\Entity\ShopInfoData;
use App\Domain\Shop\Repository\ShopRepository;
use App\Domain\User\Entity\UserGitfBoxInfo;
use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Entity\UserInventoryInfo;
use App\Domain\User\Repository\UserRepository;
use App\Exception\ErrorCode;
use Psr\Log\LoggerInterface;

class ShopService extends BaseService
{
    protected ShopRepository $shopRepository;
    protected UserRepository $userRepository;
    protected CommonRepository $commonRepository;
    protected ScribeService $scribeService;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

    private const SHOP_REDIS_KEY = 'shop:%s';
    private const USER_REDIS_KEY = 'user:%s';

    public function __construct(LoggerInterface $logger
        ,ShopRepository $shopRepository
        ,UserRepository $userRepository
        ,CommonRepository $commonRepository
        ,ScribeService $scribeService
        ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->shopRepository = $shopRepository;
        $this->userRepository = $userRepository;
        $this->commonRepository = $commonRepository;
        $this->scribeService = $scribeService;
        $this->redisService = $redisService;
    }

    public function getShopInfo(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        $myShopInfo = new ShopInfoData();
        $myShopInfo->setShopCode($data->shopCode);

        //상점 상세 조회
        $shopInfo = $this->shopRepository->getShopInfo($myShopInfo);
        $code = new ErrorCode();
        $this->logger->info("get shop info service");
        return [
            'shopInfo' => $shopInfo,
            'codeArray' => $code->getErrorArrayItem(ErrorCode::SUCCESS),
        ];
    }

    public function getShopInfoList(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        $search = new SearchInfo();
        $search->setLimit($data->limit);
        $search->setOffset($data->offset);

        //상점 목록 조회
        $shopArray = $this->shopRepository->getShopInfoList($search);
        $shopArrayCnt = $this->shopRepository->getShopInfoListCnt($search);
        $code = new ErrorCode();
        $this->logger->info("get list shop info service");
        return [
            'shopList' => $shopArray,
            'totalCount' => $shopArrayCnt,
            'codeArray' => $code->getErrorArrayItem(ErrorCode::SUCCESS),
        ];
    }

    public function sellShopInfo(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        //캐릭터 조회
        if(self::isRedisEnabled() === true){
            $userInfo = $this->getOneUserCache($data->decoded->data->userCode, self::USER_REDIS_KEY);
        }else{
            $myUserInfo = new UserInfo();
            $myUserInfo->setUserCode($data->decoded->data->userCode);
            $userInfo = $this->userRepository->getUserInfo($myUserInfo);
        }
        //팔려는 갯수
        $sellCount = $data->inventoryCount;

        //특정 인벤토리와 갯수 조회
        $myInventory = new UserInventoryInfo();
        $myInventory->setInventoryCode($data->inventoryCode);
        $myInventory->setUserCode($data->decoded->data->userCode);
        $inventoryInfo = $this->userRepository->getUserInventory($myInventory);

        //팔려고 하는 갯수가 있는지 비교
        $code = new ErrorCode();
        if($inventoryInfo->getItemCount() >= $sellCount){
            //인벤토리 아이템에 대한 상점 아이템 조회
            $myShopInfo = new ShopInfoData();
            $myShopInfo->setItemCode($inventoryInfo->getItemCode());
            $myShopInfo->setItemType($inventoryInfo->getItemType());
            $shopInfo = $this->shopRepository->getShopInfoItem($myShopInfo);

            //인벤토리 차감
            $inventoryInfo->setItemCount($inventoryInfo->getItemCount()-$sellCount);
            $this->userRepository->createUserInventoryInfo($inventoryInfo);

            //캐릭터 재화획득 수정
            if($shopInfo->getMoneyCode() == 1){
                $userInfo->setMoneyGold($userInfo->getMoneyGold() + ($sellCount * $shopInfo->getItemPrice()));
            } elseif ($shopInfo->getMoneyCode() == 2){
                $userInfo->setMoneyPearl($userInfo->getMoneyPearl() + ($sellCount * $shopInfo->getItemPrice()));
            } else {
                $userInfo->setFatigue($userInfo->getFatigue() + ($sellCount * $shopInfo->getItemPrice()));
            }
            $this->userRepository->modifyUserInfo($userInfo);

            //redis 캐릭터 정보 변경
            if (self::isRedisEnabled() === true) {
                $userInfo = $this->userRepository->getUserInfo($userInfo);
                $this->saveInCache($data->decoded->data->userCode, $userInfo, self::USER_REDIS_KEY);
            }

            //scribe 로그 남기기
            date_default_timezone_set('Asia/Seoul');
            $currentDate = date("Ymd");
            $currentTime = date("Y-m-d H:i:s");

            //상품 판매 내역 로그 남기기
            $dataJson = json_encode([
                "date" => $currentTime,
                "dateTime" => $currentTime,
                "channel_uid" => "0",
                "game" => ScribeService::PROJECT_NAME,
                "server_id" => 'KR',
                "account_id" => $data->decoded->data->accountCode,
                "account_level" => 0,
                "character_id" => $userInfo->getUserCode(),
                "character_type_id" => 0,
                "character_level" => $userInfo->getLevelCode(),
                "character_shop_id" => $shopInfo->getShopCode(),
                "character_shop_count" => $sellCount,
                "character_shop_price" => $shopInfo->getItemPrice(),
                "character_shop_price_sum" => $sellCount * $shopInfo->getItemPrice(),
                "app_id" => ScribeService::PROJECT_NAME,
                "client_ip" => $_SERVER['REMOTE_ADDR'],
                "server_ip" => $_SERVER['SERVER_ADDR'],
                "channel" => "C2S",
                "company" => "C2S",
                "guid" => $_SERVER['GUID']
            ]);

            $msg1[] = new \LogEntry(array(
                'category' => 'uruk_game_character_shop_sell_log_'.$currentDate,
                'message' => $dataJson
            ));
            $this->scribeService->Log($msg1);

            //재화 로그 남기기
            $dataJson2 = json_encode([
                "date" => $currentTime,
                "dateTime" => $currentTime,
                "channel_uid" => "0",
                "game" => ScribeService::PROJECT_NAME,
                "server_id" => 'KR',
                "account_id" => $data->decoded->data->accountCode,
                "account_level" => 0,
                "character_id" => $userInfo->getUserCode(),
                "character_type_id" => 0,
                "character_level" => $userInfo->getLevelCode(),
                "character_money_id" => $shopInfo->getMoneyCode(),
                "character_money_item_id" => $shopInfo->getShopCode(),
                "character_money_item_type" => 5,
                "character_money_type" => 1,
                "character_money_price" => $sellCount * $shopInfo->getItemPrice(),
                "app_id" => ScribeService::PROJECT_NAME,
                "client_ip" => $_SERVER['REMOTE_ADDR'],
                "server_ip" => $_SERVER['SERVER_ADDR'],
                "channel" => "C2S",
                "company" => "C2S",
                "guid" => $_SERVER['GUID']
            ]);

            $msg2[] = new \LogEntry(array(
                'category' => 'uruk_game_character_money_log_'.$currentDate,
                'message' => $dataJson2
            ));
            $this->scribeService->Log($msg2);

            return [
                'moneyCode' => $shopInfo->getMoneyCode(),
                'moneyPrice' => $sellCount * $shopInfo->getItemPrice(),
                'codeArray' => $code->getErrorArrayItem(ErrorCode::SELL_SUCCESS),
            ];
        }else{
            return [
                'codeArray' => $code->getErrorArrayItem(ErrorCode::SELL_FAIL),
            ];
        }
    }

    public function buyShopInfo(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        //캐릭터 조회
        if(self::isRedisEnabled() === true){
            $userInfo = $this->getOneUserCache($data->decoded->data->userCode, self::USER_REDIS_KEY);
        }else{
            $myUserInfo = new UserInfo();
            $myUserInfo->setUserCode($data->decoded->data->userCode);
            $userInfo = $this->userRepository->getUserInfo($myUserInfo);
        }
        $code = new ErrorCode();
        //구입할 갯수
        $buyCount = $data->shopCount;
        if($buyCount != 0){
            //특정 상점 아이템 조회
            $myShopInfo = new ShopInfoData();
            $myShopInfo->setShopCode($data->shopCode);
            $shopInfo = $this->shopRepository->getShopInfo($myShopInfo);
            //팔려고 하는 갯수가 총 인벤토리에 넘지 않는지 비교 && 재화를 충분히 가지고 있는지 비교
            if($shopInfo->getMoneyCode() == 1){
                $compareMoney = $userInfo->getMoneyGold() - ($buyCount * $shopInfo->getItemPrice());
                $userInfo->setMoneyGold($compareMoney);
            } elseif ($shopInfo->getMoneyCode() == 2){
                $compareMoney = $userInfo->getMoneyPearl() - ($buyCount * $shopInfo->getItemPrice());
                $userInfo->setMoneyPearl($compareMoney);
            } else {
                $compareMoney = $userInfo->getFatigue() - ($buyCount * $shopInfo->getItemPrice());
                $userInfo->setFatigue($compareMoney);
            }
            
            if($compareMoney >= 0){
                //상품 선물함(우편함)으로 이동
                $giftBox = new UserGitfBoxInfo();
                $giftBox->setUserCode($userInfo->getUserCode());
                $giftBox->setItemCode($shopInfo->getItemCode());
                $giftBox->setItemType($shopInfo->getItemType());
                $giftBox->setItemCount($buyCount);
                $this->userRepository->createUserGiftBoxShop($giftBox);

                //캐릭터 재화획득 소비 변경
                $this->userRepository->modifyUserInfo($userInfo);

                //redis 캐릭터 정보 변경
                if (self::isRedisEnabled() === true) {
                    $userInfo = $this->userRepository->getUserInfo($userInfo);
                    $this->saveInCache($data->decoded->data->userCode, $userInfo, self::USER_REDIS_KEY);
                }

                //scribe 로그 남기기
                date_default_timezone_set('Asia/Seoul');
                $currentDate = date("Ymd");
                $currentTime = date("Y-m-d H:i:s");

                //상품 구매 내역 로그 남기기
                $dataJson = json_encode([
                    "date" => $currentTime,
                    "dateTime" => $currentTime,
                    "channel_uid" => "0",
                    "game" => ScribeService::PROJECT_NAME,
                    "server_id" => 'KR',
                    "account_id" => $data->decoded->data->accountCode,
                    "account_level" => 0,
                    "character_id" => $userInfo->getUserCode(),
                    "character_type_id" => 0,
                    "character_level" => $userInfo->getLevelCode(),
                    "character_shop_id" => $shopInfo->getShopCode(),
                    "character_shop_count" => $buyCount,
                    "character_shop_price" => $shopInfo->getItemPrice(),
                    "character_shop_price_sum" => $buyCount * $shopInfo->getItemPrice(),
                    "app_id" => ScribeService::PROJECT_NAME,
                    "client_ip" => $_SERVER['REMOTE_ADDR'],
                    "server_ip" => $_SERVER['SERVER_ADDR'],
                    "channel" => "C2S",
                    "company" => "C2S",
                    "guid" => $_SERVER['GUID']
                ]);

                $msg1[] = new \LogEntry(array(
                    'category' => 'uruk_game_character_shop_buy_log_'.$currentDate,
                    'message' => $dataJson
                ));
                $this->scribeService->Log($msg1);

                //재화 로그 남기기
                $dataJson2 = json_encode([
                    "date" => $currentTime,
                    "dateTime" => $currentTime,
                    "channel_uid" => "0",
                    "game" => ScribeService::PROJECT_NAME,
                    "server_id" => 'KR',
                    "account_id" => $data->decoded->data->accountCode,
                    "account_level" => 0,
                    "character_id" => $userInfo->getUserCode(),
                    "character_type_id" => 0,
                    "character_level" => $userInfo->getLevelCode(),
                    "character_money_id" => $shopInfo->getMoneyCode(),
                    "character_money_item_id" => $shopInfo->getShopCode(),
                    "character_money_item_type" => 5,
                    "character_money_type" => 2,
                    "character_money_price" => $buyCount * $shopInfo->getItemPrice(),
                    "app_id" => ScribeService::PROJECT_NAME,
                    "client_ip" => $_SERVER['REMOTE_ADDR'],
                    "server_ip" => $_SERVER['SERVER_ADDR'],
                    "channel" => "C2S",
                    "company" => "C2S",
                    "guid" => $_SERVER['GUID']
                ]);

                $msg2[] = new \LogEntry(array(
                    'category' => 'uruk_game_character_money_log_'.$currentDate,
                    'message' => $dataJson2
                ));
                $this->scribeService->Log($msg2);

                return [
                    'moneyCode' => $shopInfo->getMoneyCode(),
                    'moneyPrice' => $buyCount * $shopInfo->getItemPrice(),
                    'codeArray' => $code->getErrorArrayItem(ErrorCode::BUY_SUCCESS),
                ];
            }else{
                return [
                    'moneyCode' => $shopInfo->getMoneyCode(),
                    'moneyPrice' => $buyCount * $shopInfo->getItemPrice(),
                    'codeArray' => $code->getErrorArrayItem(ErrorCode::NO_MONEY),
                ];
            }
        }else{
            return [
                'codeArray' => $code->getErrorArrayItem(ErrorCode::NO_PARAM),
            ];
        }
    }

    protected function saveInCache(int $userCode, object $user, string $redisKey): void
    {
        $redisKey = sprintf($redisKey, $userCode);
        $key = $this->redisService->generateKey($redisKey);
        //$this->redisService->setex($key, $user);
        $this->redisService->set($key, $user);
    }

    protected function deleteFromCache(int $userCode, string $redisKey): void
    {
        $redisKey = sprintf($redisKey, $userCode);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->del([$key]);
    }

    protected function getOneUserCache(int $code, string $redisKeys): UserInfo
    {
        $redisKey = sprintf($redisKeys, $code);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $model = json_decode((string)json_encode($this->redisService->get($key)), false);

            $userInfo = new UserInfo();
            $userInfo->setAccountCode($model->accountCode);
            $userInfo->setUserCode($model->userCode);
            $userInfo->setUserNickNm($model->userNickNm);
            $userInfo->setLevelCode($model->levelCode);
            $userInfo->setUserExperience($model->userExperience);
            $userInfo->setMoneyGold($model->moneyGold);
            $userInfo->setMoneyPearl($model->moneyPearl);
            $userInfo->setFatigue($model->fatigue);
            $userInfo->setUseInventoryCount($model->useInventoryCount);
            $userInfo->setUseSaveItemCount($model->useSaveItemCount);
            $userInfo->setCreateDate($model->createDate);
        } else {
            $myUserInfo = new UserInfo();
            $myUserInfo->setUserCode($code);
            $userInfo = $this->userRepository->getUserInfo($myUserInfo);
        }
        return $userInfo;
    }
}