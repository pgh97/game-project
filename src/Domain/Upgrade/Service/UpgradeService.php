<?php

namespace App\Domain\Upgrade\Service;

use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Common\Entity\Ship\ShipInfoData;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\Common\Service\ScribeService;
use App\Domain\Fishing\Repository\FishingRepository;
use App\Domain\Upgrade\Entity\FishingItemUpgradeData;
use App\Domain\Upgrade\Entity\ShipItemUpgradeData;
use App\Domain\Upgrade\Entity\UpgradeItemData;
use App\Domain\Upgrade\Repository\UpgradeRepository;
use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Entity\UserInventoryInfo;
use App\Domain\User\Entity\UserShipInfo;
use App\Domain\User\Repository\UserRepository;
use App\Exception\ErrorCode;
use Psr\Log\LoggerInterface;

class UpgradeService extends BaseService
{
    protected UpgradeRepository $upgradeRepository;
    protected UserRepository $userRepository;
    protected FishingRepository $fishingRepository;
    protected CommonRepository $commonRepository;
    protected ScribeService $scribeService;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

    private const UPGRADE_REDIS_KEY = 'upgrade:%s';
    private const USER_REDIS_KEY = 'user:%s';

    public function __construct(LoggerInterface $logger
        ,UpgradeRepository $upgradeRepository
        ,UserRepository $userRepository
        ,FishingRepository $fishingRepository
        ,CommonRepository $commonRepository
        ,ScribeService $scribeService
        ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->upgradeRepository = $upgradeRepository;
        $this->userRepository = $userRepository;
        $this->fishingRepository = $fishingRepository;
        $this->commonRepository = $commonRepository;
        $this->scribeService = $scribeService;
        $this->redisService = $redisService;
    }

    public function modifyUpgradeFishingItem(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        //????????? ??????
        if(self::isRedisEnabled() === true){
            $userInfo = $this->getOneUserCache($data->decoded->data->userCode, self::USER_REDIS_KEY);
        }else{
            $myUserInfo = new UserInfo();
            $myUserInfo->setUserCode($data->decoded->data->userCode);
            $userInfo = $this->userRepository->getUserInfo($myUserInfo);
        }
        //?????? ???????????? ??????
        $myInventory = new UserInventoryInfo();
        $myInventory->setUserCode($data->decoded->data->userCode);
        $myInventory->setInventoryCode($data->inventoryCode);
        $itemInfo = $this->userRepository->getUserInventory($myInventory);

        //?????? ???????????? ??????????????? ?????? ?????? ??????
        if($itemInfo->getItemType() == 1){
            $originalItem = $this->fishingRepository->getFishingRodGradeData($itemInfo);
        } elseif ($itemInfo->getItemType() == 2){
            $originalItem = $this->fishingRepository->getFishingLineGradeData($itemInfo);
        } else {
            $originalItem = $this->fishingRepository->getFishingReelGradeData($itemInfo);
        }

        //??????????????? ???????????? ??????
        $code = new ErrorCode();
        if($originalItem->getMaxUpgrade() > $itemInfo->getUpgradeLevel()){
            //fishing_item_upgrade_data?????? ????????? ?????? ??????
            $upgradeInfo = new FishingItemUpgradeData();
            $upgradeInfo->setUpgradeCode($itemInfo->getUpgradeCode()+1);
            $upgradeInfo = $this->upgradeRepository->getFishingItemUpgradeData($upgradeInfo);

            //??????????????? ????????? ??????????????? ????????? ?????? ????????? ??????
            $search = new SearchInfo();
            $search->setUserCode($userInfo->getUserCode());
            $search->setItemCode($upgradeInfo->getUpgradeItemCode());
            $search->setItemType(6);
            $upgradeItemCnt = $this->userRepository->getUserInventoryUpgradeItemListCnt($search);

            if($upgradeItemCnt >= $upgradeInfo->getUpgradeItemCount()){
                //????????? ????????? ?????????????????? ???????????? ??????
                if($upgradeInfo->getMoneyCode() == 1){
                    $compareMoney = $userInfo->getMoneyGold() - $upgradeInfo->getUpgradePrice();
                    $userInfo->setMoneyGold($compareMoney);
                } elseif ($upgradeInfo->getMoneyCode() == 2){
                    $compareMoney = $userInfo->getMoneyPearl() - $upgradeInfo->getUpgradePrice();
                    $userInfo->setMoneyPearl($compareMoney);
                } else {
                    $compareMoney = $userInfo->getFatigue() - $upgradeInfo->getUpgradePrice();
                    $userInfo->setFatigue($compareMoney);
                }

                if($compareMoney >= 0){
                    //???????????? ??????????????? ??????
                    $itemInfo->setUpgradeCode($upgradeInfo->getUpgradeCode());
                    $itemInfo->setUpgradeLevel($upgradeInfo->getUpgradeLevel());
                    $itemInfo->setItemDurability($originalItem->getDurability()+round($originalItem->getDurability()*$upgradeInfo->getAddProbability()/10));
                    $this->userRepository->createUserInventoryInfo($itemInfo);
                    
                    //???????????? ??????????????? ?????? ????????? ????????? ?????? ??????
                    $upgradeItemInfo = $this->userRepository->getUserInventoryUpgradeItem($search);
                    $upgradeItemInfo->setItemCount($upgradeItemInfo->getItemCount()-$upgradeInfo->getUpgradeItemCount());
                    $this->userRepository->createUserInventoryInfo($upgradeItemInfo);

                    //?????????????????? ????????? ??????
                    $this->userRepository->modifyUserInfo($userInfo);

                    //redis ????????? ?????? ??????
                    if (self::isRedisEnabled() === true) {
                        $userInfo = $this->userRepository->getUserInfo($userInfo);
                        $this->saveInCache($data->decoded->data->userCode, $userInfo, self::USER_REDIS_KEY);
                    }

                    //scribe ?????? ?????????
                    date_default_timezone_set('Asia/Seoul');
                    $currentTime = date("Y-m-d H:i:s");

                    //??????????????? ?????? ?????????
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
                        "character_upgrade_id" => $upgradeInfo->getUpgradeCode(),
                        "character_upgrade_level" => $upgradeInfo->getUpgradeLevel(),
                        "character_upgrade_price" => $upgradeInfo->getUpgradePrice(),
                        "character_upgrade_type" => 1,
                        "app_id" => ScribeService::PROJECT_NAME,
                        "client_ip" => $_SERVER['REMOTE_ADDR'],
                        "server_ip" => $_SERVER['SERVER_ADDR'],
                        "channel" => "C2S",
                        "company" => "C2S",
                        "guid" => $_SERVER['GUID']
                    ]);

                    $msg1[] = new \LogEntry(array(
                        'category' => 'uruk_game_character_upgrade_log',
                        'message' => $dataJson
                    ));
                    $this->scribeService->Log($msg1);

                    //?????? ?????? ?????????
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
                        "character_money_id" => $upgradeInfo->getMoneyCode(),
                        "character_money_item_id" => $upgradeInfo->getUpgradeCode(),
                        "character_money_item_type" => 2,
                        "character_money_type" => 2,
                        "character_money_price" => $upgradeInfo->getUpgradePrice(),
                        "app_id" => ScribeService::PROJECT_NAME,
                        "client_ip" => $_SERVER['REMOTE_ADDR'],
                        "server_ip" => $_SERVER['SERVER_ADDR'],
                        "channel" => "C2S",
                        "company" => "C2S",
                        "guid" => $_SERVER['GUID']
                    ]);

                    $msg2[] = new \LogEntry(array(
                        'category' => 'uruk_game_character_money_log',
                        'message' => $dataJson2
                    ));
                    $this->scribeService->Log($msg2);

                    return [
                        'moneyCode' => $upgradeInfo->getMoneyCode(),
                        'moneyPrice' => $upgradeInfo->getUpgradePrice(),
                        'codeArray' => $code->getErrorArrayItem(ErrorCode::UPGRADE_SUCCESS),
                    ];
                }else{
                    return [
                        'moneyCode' => $upgradeInfo->getMoneyCode(),
                        'moneyPrice' => $upgradeInfo->getUpgradePrice(),
                        'codeArray' => $code->getErrorArrayItem(ErrorCode::NO_MONEY),
                    ];
                }
            }else{
                return [
                    'codeArray' => $code->getErrorArrayItem(ErrorCode::NO_UPGRADE_ITEM),
                ];
            }
        }else{
            return [
                'codeArray' => $code->getErrorArrayItem(ErrorCode::ALREADY_FULL),
            ];
        }
    }

    public function modifyUpgradeShipItem(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        //????????? ??????
        if(self::isRedisEnabled() === true){
            $userInfo = $this->getOneUserCache($data->decoded->data->userCode, self::USER_REDIS_KEY);
        }else{
            $myUserInfo = new UserInfo();
            $myUserInfo->setUserCode($data->decoded->data->userCode);
            $userInfo = $this->userRepository->getUserInfo($myUserInfo);
        }
        //????????? ?????????24??? ??????
        $myShip = new UserShipInfo();
        $myShip->setUserCode($data->decoded->data->userCode);
        $itemInfo = $this->userRepository->getUserShipInfo($myShip);

        //?????? ?????????24??? ??????
        $originalItem = new ShipInfoData();
        $originalItem->setShipCode($itemInfo->getShipCode());
        $originalItem = $this->commonRepository->getShipInfo($originalItem);

        //??????????????? ???????????? ??????
        $code = new ErrorCode();
        if($originalItem->getMaxUpgrade() > $itemInfo->getUpgradeLevel()){
            //ship_item_upgrade_data ??????
            $upgradeInfo = new ShipItemUpgradeData();
            $upgradeInfo->setUpgradeCode($itemInfo->getUpgradeCode()+1);
            $upgradeInfo = $this->upgradeRepository->getShipItemUpgradeData($upgradeInfo);

            //????????? ????????? ?????????????????? ???????????? ??????
            if($upgradeInfo->getMoneyCode() == 1){
                $compareMoney = $userInfo->getMoneyGold() - $upgradeInfo->getUpgradePrice();
                $userInfo->setMoneyGold($compareMoney);
            } elseif ($upgradeInfo->getMoneyCode() == 2){
                $compareMoney = $userInfo->getMoneyPearl() - $upgradeInfo->getUpgradePrice();
                $userInfo->setMoneyPearl($compareMoney);
            } else {
                $compareMoney = $userInfo->getFatigue() - $upgradeInfo->getUpgradePrice();
                $userInfo->setFatigue($compareMoney);
            }
            if($compareMoney >= 0){
                //?????????24??? ??????????????? ????????????
                $probabilityArray = array(0,1); // 0:??????, 1:??????
                $percentArray = array($upgradeInfo->getUpgradeProbability(), 100-$upgradeInfo->getUpgradeProbability());
                $result = $this->Percent_draw($probabilityArray, $percentArray);
                if($result == 0){
                    //?????????24??? ??????????????? ??????
                    $itemInfo->setUpgradeCode($upgradeInfo->getUpgradeCode());
                    $itemInfo->setUpgradeLevel($upgradeInfo->getUpgradeLevel());
                    $itemInfo->setDurability($originalItem->getDurability()+($originalItem->getDurability()*$upgradeInfo->getAddProbability()));
                    $itemInfo->setFuel($originalItem->getFuel()+($originalItem->getFuel()*$upgradeInfo->getAddFuel()));
                    $this->userRepository->modifyUserShip($itemInfo);

                    //?????????????????? ????????? ??????
                    $this->userRepository->modifyUserInfo($userInfo);

                    //redis ????????? ?????? ??????
                    if (self::isRedisEnabled() === true) {
                        $userInfo = $this->userRepository->getUserInfo($userInfo);
                        $this->saveInCache($data->decoded->data->userCode, $userInfo, self::USER_REDIS_KEY);
                    }

                    //scribe ?????? ?????????
                    date_default_timezone_set('Asia/Seoul');
                    $currentTime = date("Y-m-d H:i:s");

                    //??????????????? ?????? ?????????
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
                        "character_upgrade_id" => $upgradeInfo->getUpgradeCode(),
                        "character_upgrade_type" => 2,
                        "character_upgrade_level" => $upgradeInfo->getUpgradeLevel(),
                        "character_upgrade_price" => $upgradeInfo->getUpgradePrice(),
                        "app_id" => ScribeService::PROJECT_NAME,
                        "client_ip" => $_SERVER['REMOTE_ADDR'],
                        "server_ip" => $_SERVER['SERVER_ADDR'],
                        "channel" => "C2S",
                        "company" => "C2S",
                        "guid" => $_SERVER['GUID']
                    ]);

                    $msg1[] = new \LogEntry(array(
                        'category' => 'uruk_game_character_upgrade_log',
                        'message' => $dataJson
                    ));
                    $this->scribeService->Log($msg1);

                    //?????? ?????? ?????????
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
                        "character_money_id" => $upgradeInfo->getMoneyCode(),
                        "character_money_item_id" => $upgradeInfo->getUpgradeCode(),
                        "character_money_item_type" => 3,
                        "character_money_type" => 2,
                        "character_money_price" => $upgradeInfo->getUpgradePrice(),
                        "app_id" => ScribeService::PROJECT_NAME,
                        "client_ip" => $_SERVER['REMOTE_ADDR'],
                        "server_ip" => $_SERVER['SERVER_ADDR'],
                        "channel" => "C2S",
                        "company" => "C2S",
                        "guid" => $_SERVER['GUID']
                    ]);

                    $msg2[] = new \LogEntry(array(
                        'category' => 'uruk_game_character_money_log',
                        'message' => $dataJson2
                    ));
                    $this->scribeService->Log($msg2);

                    return [
                        'moneyCode' => $upgradeInfo->getMoneyCode(),
                        'moneyPrice' => $upgradeInfo->getUpgradePrice(),
                        'codeArray' => $code->getErrorArrayItem(ErrorCode::UPGRADE_SUCCESS),
                    ];
                }else{
                    return [
                        'moneyCode' => $upgradeInfo->getMoneyCode(),
                        'moneyPrice' => $upgradeInfo->getUpgradePrice(),
                        'codeArray' => $code->getErrorArrayItem(ErrorCode::UPGRADE_FAIL),
                    ];
                }
            }else{
                return [
                    'moneyCode' => $upgradeInfo->getMoneyCode(),
                    'moneyPrice' => $upgradeInfo->getUpgradePrice(),
                    'codeArray' => $code->getErrorArrayItem(ErrorCode::NO_MONEY),
                ];
            }
        }else{
            return [
                'codeArray' => $code->getErrorArrayItem(ErrorCode::ALREADY_FULL),
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

    protected function Percent_draw($items_list, $percent_list)
    {
        $range_now = 0;
        $range_last = 0;
        $decimal = 4;
        if(count($percent_list) != count($items_list)) return false;
        $draw = mt_rand(1,pow(10,$decimal)*array_sum($percent_list));
        for($sequence=0; $sequence<count($percent_list); $sequence++) {
            $range_now += pow(10,$decimal)*$percent_list[$sequence];
            if($range_now >= $draw && $range_last < $draw) {
                return $items_list[$sequence];
            }else{
                $range_last = $range_now;
            }
        }
    }
}