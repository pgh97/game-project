<?php

namespace App\Domain\Repair\Service;

use App\Domain\Common\Entity\Level\UserLevelInfoData;
use App\Domain\Common\Entity\Ship\ShipInfoData;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\Common\Service\ScribeService;
use App\Domain\Fishing\Repository\FishingRepository;
use App\Domain\Repair\Entity\ItemRepairInfoData;
use App\Domain\Repair\Repository\RepairRepository;
use App\Domain\Upgrade\Entity\FishingItemUpgradeData;
use App\Domain\Upgrade\Entity\ShipItemUpgradeData;
use App\Domain\Upgrade\Repository\UpgradeRepository;
use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Entity\UserInventoryInfo;
use App\Domain\User\Entity\UserShipInfo;
use App\Domain\User\Repository\UserRepository;
use App\Exception\ErrorCode;
use Psr\Log\LoggerInterface;

class RepairService extends BaseService
{
    protected RepairRepository $repairRepository;
    protected UserRepository $userRepository;
    protected FishingRepository $fishingRepository;
    protected UpgradeRepository $upgradeRepository;
    protected CommonRepository $commonRepository;
    protected ScribeService $scribeService;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

    private const REPAIR_REDIS_KEY = 'repair:%s';
    private const USER_REDIS_KEY = 'user:%s';

    public function __construct(LoggerInterface $logger
        ,RepairRepository $repairRepository
        ,UserRepository $userRepository
        ,FishingRepository $fishingRepository
        ,UpgradeRepository $upgradeRepository
        ,CommonRepository $commonRepository
        ,ScribeService $scribeService
        ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->repairRepository = $repairRepository;
        $this->userRepository = $userRepository;
        $this->fishingRepository = $fishingRepository;
        $this->upgradeRepository = $upgradeRepository;
        $this->commonRepository = $commonRepository;
        $this->scribeService = $scribeService;
        $this->redisService = $redisService;
    }
    
    public function modifyRepairItem(array $input):array
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
        $code = new ErrorCode();

        //????????? ????????????(?????????, ?????????, ???) or ?????????24 ??????
        //?????? ????????? ????????? ??????
        $repairInfo = new ItemRepairInfoData();
        if ($data->itemType == 4){
            $myShip = new UserShipInfo();
            $myShip->setUserCode($data->decoded->data->userCode);
            $itemInfo = $this->userRepository->getUserShipInfo($myShip);

            //ship_item_upgrade_data ??????
            $upgradeInfo = new ShipItemUpgradeData();
            $upgradeInfo->setUpgradeCode($itemInfo->getUpgradeCode());
            $upgradeInfo = $this->upgradeRepository->getShipItemUpgradeData($upgradeInfo);

            //?????? ????????? ??????
            $originalItem = new ShipInfoData();
            $originalItem->setShipCode($itemInfo->getShipCode());
            $originalItem = $this->commonRepository->getShipInfo($originalItem);
            $originalItem->setDurability($originalItem->getDurability()+($originalItem->getDurability()*$upgradeInfo->getAddProbability()));
            $originalItem->setFuel($originalItem->getFuel()+($originalItem->getFuel()*$upgradeInfo->getAddFuel()));

            //?????? ????????? ????????? ?????? itemCode
            $repairInfo->setItemCode($itemInfo->getShipCode());
        } else {
            $myInventory = new UserInventoryInfo();
            $myInventory->setUserCode($data->decoded->data->userCode);
            $myInventory->setInventoryCode($data->itemCode);
            $itemInfo = $this->userRepository->getUserInventory($myInventory);

            //fishing_item_upgrade_data?????? ????????? ?????? ??????
            $upgradeInfo = new FishingItemUpgradeData();
            $upgradeInfo->setUpgradeCode($itemInfo->getUpgradeCode());
            $upgradeInfo = $this->upgradeRepository->getFishingItemUpgradeData($upgradeInfo);

            //?????? ??????????????? ??????
            if($data->itemType == 1){
                $originalItem = $this->fishingRepository->getFishingRodGradeData($itemInfo);
            } elseif ($data->itemType == 2){
                $originalItem = $this->fishingRepository->getFishingLineGradeData($itemInfo);
            } else {
                $originalItem = $this->fishingRepository->getFishingReelGradeData($itemInfo);
            }
            $originalItem->setDurability($originalItem->getDurability()
                +round($originalItem->getDurability()*$upgradeInfo->getAddProbability()/10));

            //?????? ????????? ????????? ?????? itemCode
            $repairInfo->setItemCode($itemInfo->getItemCode());
        }

        //item_repair_info_data ??????
        $repairInfo->setItemType($data->itemType);
        $repairInfo = $this->repairRepository->getItemRepairInfo($repairInfo);

        //????????? ????????? ????????? ??? (?????????24??? ???????????? ?????? ????????? ???????????? ????????? ?????????)
        if ($data->itemType == 4){
            $needDurability = $originalItem->getDurability() - $itemInfo->getDurability();
        }else{
            $needDurability = $originalItem->getDurability() - $itemInfo->getItemDurability();
        }

        if($needDurability != 0){
            //????????? ????????? ???????????? ???????????? ??????
            if($repairInfo->getMoneyCode() == 1){
                $compareMoney = $userInfo->getMoneyGold() - ($needDurability * $repairInfo->getRepairPrice());
                $userInfo->setMoneyGold($compareMoney);
            } elseif ($repairInfo->getMoneyCode() == 2){
                $compareMoney = $userInfo->getMoneyPearl() - ($needDurability * $repairInfo->getRepairPrice());
                $userInfo->setMoneyPearl($compareMoney);
            } else {
                $compareMoney = $userInfo->getFatigue() - ($needDurability * $repairInfo->getRepairPrice());
                $userInfo->setFatigue($compareMoney);
            }

            if($compareMoney >= 0){
                //?????? -> ?????? ???????????? ????????? ?????? or ?????????24 ????????? (?????? and ??????)
                if ($data->itemType == 4){
                    $itemInfo->setDurability($originalItem->getDurability());
                    $itemInfo->setFuel($originalItem->getFuel());
                    $this->userRepository->modifyUserShip($itemInfo);
                }else {
                    $itemInfo->setItemDurability($originalItem->getDurability());
                    $this->logger->info($itemInfo->getUpgradeCode());
                    $this->userRepository->createUserInventoryInfo($itemInfo);
                }

                //????????? ?????? ??????
                $this->userRepository->modifyUserInfo($userInfo);

                //redis ??????????????? ??????
                if (self::isRedisEnabled() === true) {
                    $userInfo = $this->userRepository->getUserInfo($userInfo);
                    $this->saveInCache($data->decoded->data->userCode, $userInfo, self::USER_REDIS_KEY);
                }

                //scribe ?????? ?????????
                date_default_timezone_set('Asia/Seoul');
                $currentTime = date("Y-m-d H:i:s");

                //?????? ?????? ?????? ?????????
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
                    "character_repair_id" => $repairInfo->getRepairCode(),
                    "character_repair_price" => $repairInfo->getRepairPrice(),
                    "character_repair_durability" => $needDurability,
                    "character_repair_sum" => $needDurability * $repairInfo->getRepairPrice(),
                    "app_id" => ScribeService::PROJECT_NAME,
                    "client_ip" => $_SERVER['REMOTE_ADDR'],
                    "server_ip" => $_SERVER['SERVER_ADDR'],
                    "channel" => "C2S",
                    "company" => "C2S",
                    "guid" => $_SERVER['GUID']
                ]);

                $msg1[] = new \LogEntry(array(
                    'category' => 'uruk_game_character_repair_log',
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
                    "character_money_id" => $repairInfo->getMoneyCode(),
                    "character_money_item_id" => $repairInfo->getRepairCode(),
                    "character_money_item_type" => 4,
                    "character_money_type" => 2,
                    "character_money_price" => $needDurability * $repairInfo->getRepairPrice(),
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
                    'moneyCode' => $repairInfo->getMoneyCode(),
                    'moneyPrice' => $needDurability * $repairInfo->getRepairPrice(),
                    'codeArray' => $code->getErrorArrayItem(ErrorCode::FULL_SUCCESS),
                ];
            } else {
                return [
                    'moneyCode' => $repairInfo->getMoneyCode(),
                    'moneyPrice' => $needDurability * $repairInfo->getRepairPrice(),
                    'codeArray' => $code->getErrorArrayItem(ErrorCode::NO_MONEY),
                ];
            }
        }else{
            return [
                'codeArray' => $code->getErrorArrayItem(ErrorCode::ALREADY_FULL),
            ];
        }
    }
    
    public function modifyRepairUser(array $input):array
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
        //user_level_info_data ?????? ????????? ??????
        $myLevelInfo = new UserLevelInfoData();
        $myLevelInfo->setLevelCode($userInfo->getLevelCode());
        $levelInfo = $this->userRepository->getUserLevelInfo($myLevelInfo);
        //????????? ????????? ???????????? ???????????? ??????
        $fatigue = $levelInfo->getMaxFatigue() - $userInfo->getFatigue();
        $code = new ErrorCode();
        if($fatigue != 0){
            if($userInfo->getMoneyGold() >= ($fatigue * 10)){
                //????????? ?????? ?????? ??? ????????? ??????
                $userInfo->setMoneyGold($userInfo->getMoneyGold() - ($fatigue * 10));
                $userInfo->setFatigue($levelInfo->getMaxFatigue());
                $this->userRepository->modifyUserInfo($userInfo);
                //redis ??????????????? ??????
                if (self::isRedisEnabled() === true) {
                    $userInfo = $this->userRepository->getUserInfo($userInfo);
                    $this->saveInCache($data->decoded->data->userCode, $userInfo, self::USER_REDIS_KEY);
                }

                //scribe ?????? ?????????
                date_default_timezone_set('Asia/Seoul');
                $currentTime = date("Y-m-d H:i:s");

                //?????? ?????? ?????? ?????????
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
                    "character_repair_id" => 0,
                    "character_repair_price" => 10,
                    "character_repair_durability" => $fatigue,
                    "character_repair_sum" => $fatigue * 10,
                    "app_id" => ScribeService::PROJECT_NAME,
                    "client_ip" => $_SERVER['REMOTE_ADDR'],
                    "server_ip" => $_SERVER['SERVER_ADDR'],
                    "channel" => "C2S",
                    "company" => "C2S",
                    "guid" => $_SERVER['GUID']
                ]);

                $msg1[] = new \LogEntry(array(
                    'category' => 'uruk_game_character_repair_log',
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
                    "character_money_id" => 1,
                    "character_money_item_id" => 0,
                    "character_money_item_type" => 4,
                    "character_money_type" => 2,
                    "character_money_price" => $fatigue * 10,
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
                    'moneyCode' => 1,
                    'moneyPrice' => $fatigue * 10,
                    'codeArray' => $code->getErrorArrayItem(ErrorCode::FULL_SUCCESS),
                ];
            }else{
                return [
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
}