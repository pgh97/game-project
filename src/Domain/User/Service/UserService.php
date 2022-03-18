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
use App\Domain\User\Entity\UserGitfBoxInfo;
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

        //new 캐릭터 생성
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

        //채비 등록
        $myUserInfo = new UserInfo();
        $myUserInfo->setUserCode($data->decoded->data->userCode);
        $userinfo = $this->userRepository->getUserInfo($myUserInfo);

        //채비 총카운트 조회
        $search = new SearchInfo();
        $search->setUserCode($data->decoded->data->userCode);
        $totalCount = $this->userRepository->getUserFishingItemListCnt($search);

        //캐릭터별 채비 최대 등록 카운트 비교
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
        
        //캐릭터 정보 조회
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
            $this->userRepository->createUserWeather($weatherHistoryInfo);
            $weatherHistoryInfo = $this->userRepository->getUserWeatherHistory($weatherHistory);
            if (self::isRedisEnabled() === true) {
                $this->saveInCache($weatherHistoryInfo->getWeatherHistoryCode(), $weatherHistoryInfo, self::WEATHER_REDIS_KEY);
            }

            //인벤토리 물고기 신선도(내구도) 일괄 수정
            $myInventoryInfo = new UserInventoryInfo();
            $myInventoryInfo->setUserCode($data->userCode);
            $myInventoryInfo->setItemType(8);
            $this->userRepository->modifyUserInventoryFishDurability($myInventoryInfo);

            $token = [
                'iss' => "http://localhost:8888",
                'iat' => time(),
                'nbf' => time(),
                'exp' => time() + (7 * 24 * 60 * 60),
                'data' => [
                    'accountCode' => $data->decoded->data->accountCode,
                    'accountId' => $data->decoded->data->accountId,
                    'userCode' => $userInfo->getUserCode(),
                    'weatherHistoryCode' => $weatherHistoryInfo->getWeatherHistoryCode(),
                ]
            ];

            $payload['Authorization'] = 'Bearer ' .JWT::encode($token, $_SERVER['SECRET_KEY'], 'HS256');
            $payload['userInfo'] = $userInfo;

            $this->logger->info("get choice user service ");
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

        //캐릭터 목록 조회
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

        //캐릭터 정보 수정
        $this->userRepository->modifyUserInfo($myUserInfo);
        $result = $this->userRepository->getUserInfo($myUserInfo);

        if (self::isRedisEnabled() === true) {
            $this->saveInCache($data->decoded->data->userCode, $result, self::USER_REDIS_KEY);
        }
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
        //캐릭터별 날씨 정보 (redis cache)
        if(self::isRedisEnabled()==true){
            $weatherHistoryInfo = $this->getOneWeatherCache($data->decoded->data->weatherHistoryCode, $data->decoded->data->userCode,self::WEATHER_REDIS_KEY);
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

        //날씨의 풍량과 온도 랜덤 변경
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
        // 날씨 정보 조회
        if(self::isRedisEnabled()==true){
            $weatherInfo = $this->getOneWeatherCache($data->decoded->data->weatherHistoryCode, $data->decoded->data->userCode,self::WEATHER_REDIS_KEY);
        }else{
            $myWeatherInfo = new UserWeatherHistory();
            $myWeatherInfo->setWeatherHistoryCode($data->decoded->data->weatherHistoryCode);
            $myWeatherInfo->setUserCode($data->decoded->data->userCode);
            $weatherInfo = $this->userRepository->getUserWeatherHistory($myWeatherInfo);
        }

        $this->logger->info("get user weather service");
        return $weatherInfo;
    }

    public function getUserShipInfo(array $input): UserShipInfo
    {
        $data = json_decode((string) json_encode($input), false);
        $myUserShipInfo = new UserShipInfo();
        $myUserShipInfo->setUserCode($data->decoded->data->userCode);

        //캐릭터 보로롱24 정보 조회
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

        //캐릭터 인벤토리 목록 조회
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
        $myUserInventory->setInventoryCode($data->InventoryCode);

        //캐릭터 인벤토리 상세 조회
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
        //캐릭터 삭제
        $resultCode = $this->userRepository->deleteUserInfo($myUserInfo);
        //redis 삭제 추가해야함.
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
        //캐릭터 인벤토리 삭제
        $myUserInventory->setInventoryCode($data->inventoryCode);
        $resultCode = $this->userRepository->deleteUserInventory($myUserInventory);
        $this->logger->info("delete user inventory service");
        return $resultCode;
    }

    public function getUserGiftBox(array $input): UserGitfBoxInfo
    {
        $data = json_decode((string) json_encode($input), false);
        $myBoxInfo = new UserGitfBoxInfo();
        $myBoxInfo->setUserCode($data->decoded->data->userCode);
        $myBoxInfo->setBoxCode($data->boxCode);

        //캐릭터 선물함(우편함) 상세 조회
        $boxInfo = $this->userRepository->getUserGiftBoxInfo($myBoxInfo);
        $this->logger->info("get user gift box service");
        return $boxInfo;
    }

    public function getUserGiftBoxList(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $search = new SearchInfo();
        $search->setUserCode($data->decoded->data->userCode);
        $search->setLimit($data->limit);
        $search->setOffset($data->offset);

        //캐릭터 선물함(우편함) 목록 조회
        $boxArray = $this->userRepository->getUserGiftBoxList($search);
        $boxArrayCnt = $this->userRepository->getUserGiftBoxListCnt($search);
        $this->logger->info("get list user gift box service");
        return [
            'userGiftBoxList' => $boxArray,
            'totalCount' => $boxArrayCnt,
        ];
    }

    public function getUserFishingItem(array $input): UserChoiceItemInfo
    {
        $data = json_decode((string) json_encode($input), false);
        $myChoiceInfo = new UserChoiceItemInfo();
        $myChoiceInfo->setUserCode($data->decoded->data->userCode);
        $myChoiceInfo->setChoiceCode($data->choiceCode);

        //캐릭터 채비 상세 조회
        $choiceInfo = $this->userRepository->getUserFishingItem($myChoiceInfo);
        $this->logger->info("get user fishing item service");
        return $choiceInfo;
    }

    public function getUserFishingItemList(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $search = new SearchInfo();
        $search->setUserCode($data->decoded->data->userCode);
        $search->setLimit($data->limit);
        $search->setOffset($data->offset);

        //캐릭터 채비 목록 조회
        $fishingItemArray = $this->userRepository->getUserFishingItemList($search);
        $fishingItemArrayCnt = $this->userRepository->getUserFishingItemListCnt($search);
        $this->logger->info("get list user fishing item service");
        return [
            'userFishingItemList' => $fishingItemArray,
            'totalCount' => $fishingItemArrayCnt,
        ];
    }

    public function modifyUserGiftBox(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        $myBoxInfo = new UserGitfBoxInfo();
        $myBoxInfo->setUserCode($data->decoded->data->userCode);

        if(self::isRedisEnabled() === true){
            $userInfo = $this->getOneUserCache($data->decoded->data->userCode, self::USER_REDIS_KEY);
        }else{
            $myUserInfo = new UserInfo();
            $myUserInfo->setUserCode($data->decoded->data->userCode);
            $userInfo = $this->userRepository->getUserInfo($myUserInfo);
        }

        //인벤토리 카운트와 선물 아이템 카운트
        $search = new SearchInfo();
        $search->setUserCode($data->decoded->data->userCode);
        $inventoryCnt = $this->userRepository->getUserInventoryListCnt($search);

        $search->setItemType(99);
        $boxCnt = $this->userRepository->getUserGiftBoxListCnt($search);

        if(!empty($data->boxCode)){
            $myBoxInfo->setBoxCode($data->boxCode);
            //선물
            $boxInfo = $this->userRepository->getUserGiftBoxInfo($myBoxInfo);
            if($boxInfo->getReadStatus() == 0){
                //캐릭터 재화 추가
                if($boxInfo->getItemType()==99){
                    if($boxInfo->getItemCode()==1){
                        $userInfo->setMoneyGold($userInfo->getMoneyGold()+$boxInfo->getItemCount());
                    }elseif ($boxInfo->getItemCode()==2){
                        $userInfo->setMoneyPearl($userInfo->getMoneyPearl()+$boxInfo->getItemCount());
                    }else{
                        $userInfo->setFatigue($userInfo->setFatigue($userInfo->getFatigue()+$boxInfo->getItemCount()));
                    }
                    $this->userRepository->modifyUserInfo($userInfo);
                }else{
                    //인벤토리 여부 확인, 인벤토리 코드 return
                    $search->setItemCode($boxInfo->getItemCode());
                    $search->setItemType($boxInfo->getItemType());
                    $inventoryCode = $this->userRepository->getUserInventoryCode($search);

                    $myInventory = new UserInventoryInfo();
                    $myInventory->setUserCode($boxInfo->getUserCode());
                    if ($inventoryCode != 0){
                        //인벤토리 조회
                        $myInventory->setInventoryCode($inventoryCode);
                        $inventoryInfo = $this->userRepository->getUserInventory($myInventory);

                        //인벤토리 등록
                        $inventoryInfo->setItemCount($inventoryInfo->getItemCount()+$boxInfo->getItemCount());
                        //인벤토리 등록
                        $this->userRepository->createUserInventoryInfo($inventoryInfo);
                    }else{
                        $myInventory->setItemCode($boxInfo->getItemCode());
                        $myInventory->setItemType($boxInfo->getItemType());
                        $myInventory->setUpgradeCode(0);
                        $myInventory->setUpgradeLevel(0);
                        $myInventory->setItemCount($boxInfo->getItemCount());
                        $myInventory->setItemDurability(1);
                        //인벤토리 등록
                        $this->userRepository->createUserInventoryInfo($myInventory);
                    }
                }
                //선물함(우편함) 읽음으로 수정
                $this->userRepository->modifyUserGiftBoxStatus($boxInfo);
                if (self::isRedisEnabled() === true) {
                    $user = $this->userRepository->getUserInfo($userInfo);
                    $this->saveInCache($user->getUserCode(), $user, self::USER_REDIS_KEY);
                }
                return [
                    'message' => "선물을 받았습니다.",
                ];
            }else{
                return [
                    'message' => "선물 이미 받았습니다.",
                ];
            }
        }else{
            $myBoxInfo->setBoxCode(0);
            if($userInfo->getUseInventoryCount() >= ($inventoryCnt+$boxCnt)){
                //캐릭터 재화 추가
                $this->userRepository->modifyUserInfoGiftBox($myBoxInfo);
                //인벤토리 등록
                $this->userRepository->createUserGiftBoxToInventory($myBoxInfo);
                //선물함(우편함) 읽음으로 수정
                $this->userRepository->modifyUserGiftBoxStatus($myBoxInfo);
                if (self::isRedisEnabled() === true) {
                    $user = $this->userRepository->getUserInfo($userInfo);
                    $this->saveInCache($user->getUserCode(), $user, self::USER_REDIS_KEY);
                }
                return [
                    'message' => "선물들을 모두 받았습니다.",
                ];
            }else{
                return [
                    'message' => "선물(아이템)을 받으면 인벤토리가 넘칩니다. 인벤토리를 정리해주세요.",
                ];
            }
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

    //날씨 갱신 시간 비교
    public function weatherChange(WeatherInfoData $weatherInfoData, UserWeatherHistory $userWeatherHistory): UserWeatherHistory
    {
        date_default_timezone_set('Asia/Seoul');
        $currentTime = date("Y-m-d H:i:s");
        $windTimeDif = strtotime($currentTime) - strtotime($userWeatherHistory->getWindUpdateDate());
        $temperatureTimeDif = strtotime($currentTime) - strtotime($userWeatherHistory->getTemperatureUpdateDate());
        $temperature = $userWeatherHistory->getTemperature();

        //풍량 랜덤 갱신
        if(floor($windTimeDif / (60*60)) >= $weatherInfoData->getWindChangeTime()){
            $userWeatherHistory->setWind(rand($weatherInfoData->getMinWind(), $weatherInfoData->getMaxWind()));
            $userWeatherHistory->setWindUpdateDate("1");
        }else{
            $userWeatherHistory->setWindUpdateDate("");
        }
        //온도 랜덤 갱신 오차 +-5
        if(floor($temperatureTimeDif / (60*60)) >= $weatherInfoData->getTemperatureChangeTime()){
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

    protected function getOneWeatherCache(int $code, int $userCode, string $redisKeys): UserWeatherHistory
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
            $myWeatherInfo->setUserCode($userCode);
            $weatherInfo = $this->userRepository->getUserWeatherHistory($myWeatherInfo);
        }
        return $weatherInfo;
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