<?php

namespace App\Domain\User\Service;

use App\Domain\Common\Entity\Level\UserLevelInfoData;
use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Common\Entity\Ship\ShipInfoData;
use App\Domain\Common\Entity\Weather\WeatherInfoData;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\User\Entity\UserChoiceItemInfo;
use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Entity\UserInventoryInfo;
use App\Domain\User\Entity\UserShipInfo;
use App\Domain\User\Entity\UserWeatherHistory;
use App\Domain\User\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Firebase\JWT\JWT;

class UserService extends BaseService
{
    protected UserRepository $userRepository;
    protected CommonRepository $commonRepository;
    protected LoggerInterface $logger;
    protected RedisService $redisService;

    private const USER_REDIS_KEY = 'user:%s';
    private const WEATHER_REDIS_KEY = 'weather:%s';

    public function __construct(LoggerInterface $logger
        , UserRepository                        $userRepository
        , CommonRepository                      $commonRepository
        , RedisService                          $redisService)
    {
        $this->logger = $logger;
        $this->userRepository = $userRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }

    public function createUserInfo(array $input): int
    {
        $data = json_decode((string) json_encode($input), false);
        $myUserInfo = new UserInfo();

        $myUserInfo->setAccountCode($data->decoded->data->accountCode);
        $myUserInfo->setUserNickNm($data->user_nicknm);
        $myUserInfo->setUserExperience(0);
        $myUserInfo->setMoneyGold(100);
        $myUserInfo->setMoneyPearl(10);
        $myUserInfo->setUseSaveItemCount(5);

        $myLevelInfo = new UserLevelInfoData();
        $myLevelInfo->setLevelCode(1);
        $levelInfo = $this->userRepository->getUserLevelInfo($myLevelInfo);

        $myUserInfo->setLevelCode($levelInfo->getLevelCode());
        $myUserInfo->setFatigue($levelInfo->getMaxFatigue());
        $myUserInfo->setUseInventoryCount($levelInfo->getInventoryCount());

        $userCode = $this->userRepository->createUserInfo($myUserInfo);
        if($userCode > 0){
            //기본 아이템 인벤토리 등록
            for ($i=0; $i<5; $i++){
                $inventoryInfo = new UserInventoryInfo();
                $inventoryInfo->setUserCode($userCode);
                $inventoryInfo->setItemCode(1);
                $inventoryInfo->setItemType($i+1);
                if($i==2 || $i==3){
                    $inventoryInfo->setItemCount(10);
                    $inventoryInfo->setItemDurability(1);
                }else{
                    if($i==0){
                        $inventoryInfo->setUpgradeCode(1);
                        $inventoryInfo->setUpgradeLevel(1);
                    } else if($i==1){
                        $inventoryInfo->setUpgradeCode(52);
                        $inventoryInfo->setUpgradeLevel(1);
                    } else if($i==4){
                        $inventoryInfo->setUpgradeCode(103);
                        $inventoryInfo->setUpgradeLevel(1);
                    }
                    $inventoryInfo->setItemCount(1);
                    $inventoryInfo->setItemDurability(15);
                }
                $this->userRepository->createUserInventoryInfo($inventoryInfo);
            }

            //캐릭터별 낚시배 설정
            $myShipInfo = new ShipInfoData();
            $myShipInfo->setShipCode(1);
            $shipInfo = $this->commonRepository->getShipInfo($myShipInfo);

            $myUserShip = new UserShipInfo();
            $myUserShip->setUserCode($userCode);
            $myUserShip->setShipCode($shipInfo->getShipCode());
            $myUserShip->setDurability($shipInfo->getDurability());
            $myUserShip->setFuel($shipInfo->getFuel());
            $myUserShip->setUpgradeCode(1);
            $myUserShip->setUpgradeLevel(1);
            $this->userRepository->createUserShipInfo($myUserShip);

            //redis user 정보 등록
            if (self::isRedisEnabled() === true) {
                $myUserInfo->setUserCode($userCode);
                $user = $this->userRepository->getUserInfo($myUserInfo);
                $this->saveInCache($userCode, $user, self::USER_REDIS_KEY);
            }
            $this->logger->info("create user service");
        }
        return $userCode;
    }

    public function createUserFishingItem(array $input): int
    {
        $data = json_decode((string) json_encode($input), false);
        $myChoiceItem = new UserChoiceItemInfo();
        $myChoiceItem->setUserCode($data->decoded->data->userCode);
        $myChoiceItem->setFishingRodCode($data->fishingRodCode);
        $myChoiceItem->setFishingLineCode($data->fishingLineCode);
        $myChoiceItem->setFishingNeedleCode($data->fishingNeedleCode);
        $myChoiceItem->setFishingBaitCode($data->fishingBaitCode);
        $myChoiceItem->setFishingReelCode($data->fishingReelCode);
        $myChoiceItem->setFishingItemCode1($data->fishingItemCode1);
        $myChoiceItem->setFishingItemCode2($data->fishingItemCode2);
        $myChoiceItem->setFishingItemCode3($data->fishingItemCode3);
        $myChoiceItem->setFishingItemCode4($data->fishingItemCode4);

        $myUserInfo = new UserInfo();
        $myUserInfo->setUserCode($data->decoded->data->userCode);
        $userinfo = $this->userRepository->getUserInfo($myUserInfo);

        $search = new SearchInfo();
        $search->setUserCode($data->decoded->data->userCode);
        $totalCount = $this->userRepository->getUserFishingItemListCnt($search);
        if($userinfo->getUseSaveItemCount() > $totalCount){
            $choiceCode = $this->userRepository->createUserFishingItem($myChoiceItem);
            $this->logger->info("create user fishing-item Service");
        }else{
            $choiceCode = 0;
            $this->logger->info("count fail create user fishing-item Service");
        }
        return $choiceCode;
    }

    public function getUserInfo(array $input): object
    {
        $data = json_decode((string) json_encode($input), false);
        $myUserInfo = new UserInfo();
        if(!empty($data->decoded->data->userCode)){
            $myUserInfo->setUserCode($data->decoded->data->userCode);
        }else{
            $myUserInfo->setUserCode($data->userCode);
        }
        $myUserInfo->setAccountCode($data->decoded->data->accountCode);

        $userInfo = $this->userRepository->getUserInfo($myUserInfo);
        $this->logger->info("get user service");
        return $userInfo->toJson();
    }

    public function getUserInfoChoice(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $myUserInfo = new UserInfo();
        $myUserInfo->setAccountCode($data->decoded->data->accountCode);
        $myUserInfo->setUserCode($data->userCode);

        $userInfo = $this->userRepository->getUserInfo($myUserInfo);
        $payload = array();

        if(!empty($userInfo)){
            //캐릭터별 날씨정보 히스토리 가져오기
            $weatherHistory = new UserWeatherHistory();
            $weatherHistory->setUserCode($userInfo->getUserCode());
            $weatherHistoryInfo = $this->userRepository->getUserWeatherHistory($weatherHistory);

            //날씨 정보
            $weatherCode = new WeatherInfoData();
            if($weatherHistoryInfo->getWeatherHistoryCode() == 0){
                //날씨 정보 랜덤
                $weatherRandom = rand(1,6);
                $weatherCode->setWeatherCode($weatherRandom);
                $weatherInfo = $this->commonRepository->getWeatherInfo($weatherCode);

                $weatherHistoryInfo->setUserCode($userInfo->getUserCode());
                $weatherHistoryInfo->setWeatherCode($weatherInfo->getWeatherCode());
                $weatherHistoryInfo->setTemperature(rand($weatherInfo->getMinTemperature(), $weatherInfo->getMaxTemperature()));
                $weatherHistoryInfo->setWind(rand($weatherInfo->getMinWind(), $weatherInfo->getMaxWind()));
            }else{
                //현재 날씨정보 가져오기
                $weatherCode->setWeatherCode($weatherHistoryInfo->getWeatherCode());
                $weatherInfo = $this->commonRepository->getWeatherInfo($weatherCode);
                //날씨 갱신 시간 체크
                $weatherHistoryInfo = $this->weatherChange($weatherInfo, $weatherHistoryInfo);
            }
            //회원별 날씨 정보 등록, 수정
            $weatherHistoryCode = $this->userRepository->createUserWeather($weatherHistoryInfo);
            if (self::isRedisEnabled() === true) {
                $weatherHistory->setUserCode($userInfo->getUserCode());
                $weatherHistoryInfo = $this->userRepository->getUserWeatherHistory($weatherHistory);
                $this->saveInCache($weatherHistoryCode, $weatherHistoryInfo, self::WEATHER_REDIS_KEY);
            }

            $token = [
                'iss' => "http://localhost:8888",
                'iat' => time(),
                'nbf' => time(),
                'exp' => time() + (7 * 24 * 60 * 60),
                'data' => [
                    'accountCode' => $data->decoded->data->accountCode,
                    'accountId' => $data->decoded->data->accountId,
                    'userCode' => $userInfo->getUserCode(),
                    'weatherHistoryCode' => $weatherHistoryCode,
                ]
            ];

            $payload['Authorization'] = 'Bearer ' .JWT::encode($token, $_SERVER['SECRET_KEY'], 'HS256');
            $payload['userInfo'] = $userInfo;

            $this->logger->info("get choice user service");
        }
        return $payload;
    }

    public function getUserInfoList(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $search = new SearchInfo();
        $search->setAccountCode($data->decoded->data->accountCode);
        $search->setLimit($data->limit);
        $search->setOffset($data->offset);

        $userInfoArray = $this->userRepository->getUserInfoList($search);
        $userInfoArrayCnt = $this->userRepository->getUserInfoListCnt($search);

        $this->logger->info("get list user service");
        if(array_filter($userInfoArray)){
            return [
                'userInfoList' => $userInfoArray,
                'totalCount' => $userInfoArrayCnt,
            ];
        }else{
            return [];
        }
    }

    public function modifyUserInfo(array $input): UserInfo
    {
        $data = json_decode((string) json_encode($input), false);
        $myUserInfo = new UserInfo();
        $myUserInfo->setAccountCode($data->decoded->data->accountCode);
        if(!empty($data->decoded->data->userCode)){
            $myUserInfo->setUserCode($data->decoded->data->userCode);
        }else{
            $myUserInfo->setUserCode($data->userCode);
        }
        $myUserInfo->setUserNickNm($data->userNickNm);
        $myUserInfo->setLevelCode($data->levelCode);
        $myUserInfo->setUserExperience($data->userExperience);
        $myUserInfo->setMoneyGold($data->moneyGold);
        $myUserInfo->setMoneyPearl($data->moneyPearl);
        $myUserInfo->setFatigue($data->fatigue);

        $this->userRepository->modifyUserInfo($myUserInfo);
        $result = $this->userRepository->getUserInfo($myUserInfo);
        $this->logger->info("update user service");
        return $result;
    }

    public function modifyUserWeatherInfo(array $input): UserWeatherHistory
    {
        $data = json_decode((string) json_encode($input), false);
        /*$myWeatherInfo = new UserWeatherHistory();
        $myWeatherInfo->setWeatherHistoryCode($data->decoded->data->weatherHistoryCode);
        $myWeatherInfo->setUserCode($data->decoded->data->userCode);
        //캐릭터별 날씨 정보
        $weatherHistoryInfo = $this->userRepository->getUserWeatherHistory($myWeatherInfo);*/

        if(self::isRedisEnabled()==true){
            $weatherHistoryInfo = $this->getOneWeatherCache($data->decoded->data->weatherHistoryCode, self::WEATHER_REDIS_KEY);
        }else{
            $myWeatherInfo = new UserWeatherHistory();
            $myWeatherInfo->setWeatherHistoryCode($data->decoded->data->weatherHistoryCode);
            $myWeatherInfo->setUserCode($data->decoded->data->userCode);
            $weatherHistoryInfo = $this->userRepository->getUserWeatherHistory($myWeatherInfo);
        }

        //날씨 정보
        $weatherCode = new WeatherInfoData();
        $weatherCode->setWeatherCode($weatherHistoryInfo->getWeatherCode());
        $weatherInfo = $this->commonRepository->getWeatherInfo($weatherCode);

        $weatherHistoryInfo = $this->weatherChange($weatherInfo, $weatherHistoryInfo);
        $this->userRepository->modifyUserWeatherHistory($weatherHistoryInfo);
        $result = $this->userRepository->getUserWeatherHistory($weatherHistoryInfo);

        if(self::isRedisEnabled()==true){
            $this->saveInCache($data->decoded->data->weatherHistoryCode, $result, self::WEATHER_REDIS_KEY);
        }

        $this->logger->info("update user weather service");
        return $result;
    }

    public function getUserWeatherInfo(array $input): UserWeatherHistory
    {
        $data = json_decode((string) json_encode($input), false);
        $myWeatherInfo = new UserWeatherHistory();
        $myWeatherInfo->setUserCode($data->decoded->data->userCode);
        $weatherInfo = $this->userRepository->getUserWeatherHistory($myWeatherInfo);
        $this->logger->info("get user weather service");
        return $weatherInfo;
    }

    public function getUserShipInfo(array $input): UserShipInfo
    {
        $data = json_decode((string) json_encode($input), false);
        $myUserShipInfo = new UserShipInfo();
        $myUserShipInfo->setUserCode($data->decoded->data->userCode);

        $userShipInfo = $this->userRepository->getUserShipInfo($myUserShipInfo);
        $this->logger->info("get user ship service");
        return $userShipInfo;
    }

    public function getUserInventoryList(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $search = new SearchInfo();
        $search->setUserCode($data->decoded->data->userCode);
        $search->setLimit($data->limit);
        $search->setOffset($data->offset);

        $userInventoryArray = $this->userRepository->getUserInventoryList($search);
        $userInventoryArrayCnt = $this->userRepository->getUserInventoryListCnt($search);

        $this->logger->info("get list user inventory service");
        if(array_filter($userInventoryArray)){
            return [
                'userInventoryList' => $userInventoryArray,
                'totalCount' => $userInventoryArrayCnt,
            ];
        }else{
            return [];
        }
    }

    public function getUserInventory(array $input): UserInventoryInfo
    {
        $data = json_decode((string) json_encode($input), false);
        $myUserInventory = new UserInventoryInfo();
        $myUserInventory->setUserCode($data->decoded->data->userCode);

        $userInventory = $this->userRepository->getUserInventory($myUserInventory);
        $this->logger->info("get user inventory service");
        return $userInventory;
    }

    public function removeUserInfo(array $input): int
    {
        $data = json_decode((string) json_encode($input), false);
        $myUserInfo = new UserInfo();
        if(!empty($data->decoded->data->userCode)){
            $myUserInfo->setUserCode($data->decoded->data->userCode);
        }else{
            $myUserInfo->setUserCode($data->userCode);
        }
        $resultCode = $this->userRepository->deleteUserInfo($myUserInfo);
        $this->logger->info("delete user info service");
        return $resultCode;
    }

    public function removeUserInventory(array $input): int
    {
        $data = json_decode((string) json_encode($input), false);
        $myUserInventory = new UserInventoryInfo();
        if(!empty($data->decoded->data->userCode)){
            $myUserInventory->setUserCode($data->decoded->data->userCode);
        }else{
            $myUserInventory->setUserCode($data->userCode);
        }
        $myUserInventory->setInventoryCode($data->inventoryCode);
        $resultCode = $this->userRepository->deleteUserInventory($myUserInventory);
        $this->logger->info("delete user inventory service");
        return $resultCode;
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

    //날씨 갱신 시간 비교
    public function weatherChange(WeatherInfoData $weatherInfoData, UserWeatherHistory $userWeatherHistory): UserWeatherHistory
    {
        date_default_timezone_set('Asia/Seoul');
        $currentTime = date("Y-m-d H:i:s");
        $windTimeDif = strtotime($currentTime) - strtotime($userWeatherHistory->getWindUpdateDate());
        $temperatureTimeDif = strtotime($currentTime) - strtotime($userWeatherHistory->getTemperatureUpdateDate());
        $temperature = $userWeatherHistory->getTemperature();

        //풍량 랜덤 갱신
        if(ceil($windTimeDif / (60*60)) >= $weatherInfoData->getWindChangeTime()){
            $userWeatherHistory->setWind(rand($weatherInfoData->getMinWind(), $weatherInfoData->getMaxWind()));
            $userWeatherHistory->setWindUpdateDate("1");
        }else{
            $userWeatherHistory->setWindUpdateDate("");
        }
        //온도 랜덤 갱신 오차 +-5
        if(ceil($temperatureTimeDif / (60*60)) >= $weatherInfoData->getTemperatureChangeTime()){
            $max = $temperature+5;
            $min = $temperature-5;
            if($max >= $weatherInfoData->getMaxTemperature()){
                $max = $weatherInfoData->getMaxTemperature();
            }
            if($min <= $weatherInfoData->getMinTemperature()){
                $min = $weatherInfoData->getMinTemperature();
            }
            $userWeatherHistory->setTemperature(rand($min,$max));
            $userWeatherHistory->setTemperatureUpdateDate("1");
        }else{
            $userWeatherHistory->setTemperatureUpdateDate("");
        }
        return $userWeatherHistory;
    }

    protected function getOneWeatherCache(int $code, string $redisKeys): UserWeatherHistory
    {
        $redisKey = sprintf($redisKeys, $code);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $model = json_decode((string)json_encode($this->redisService->get($key)), false);

            $weatherInfo = new UserWeatherHistory();
            $weatherInfo->setWeatherHistoryCode($model->weatherHistoryCode);
            $weatherInfo->setUserCode($model->userCode);
            $weatherInfo->setWeatherCode($model->weatherCode);
            $weatherInfo->setTemperature($model->temperature);
            $weatherInfo->setWind($model->wind);
            $weatherInfo->setMapCode($model->mapCode);
            $weatherInfo->setCreateDate($model->createDate);
            $weatherInfo->setMapUpdateDate($model->mapUpdateDate);
            $weatherInfo->setWindUpdateDate($model->windUpdateDate);
            $weatherInfo->setTemperatureUpdateDate($model->temperatureUpdateDate);
        } else {
            $myWeatherInfo = new UserWeatherHistory();
            $myWeatherInfo->setUserCode($code);
            $weatherInfo = $this->userRepository->getUserWeatherHistory($myWeatherInfo);
        }
        return $weatherInfo;
    }
}