<?php

namespace App\Domain\User\Service;

use App\Domain\Common\Entity\Level\UserLevelInfoData;
use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Common\Entity\Ship\ShipInfoData;
use App\Domain\Common\Entity\Weather\WeatherInfoData;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\Common\Service\ScribeService;
use App\Domain\Fishing\Repository\FishingRepository;
use App\Domain\Upgrade\Entity\FishingItemUpgradeData;
use App\Domain\Upgrade\Repository\UpgradeRepository;
use App\Domain\User\Entity\UserChoiceItemInfo;
use App\Domain\User\Entity\UserFishDictionary;
use App\Domain\User\Entity\UserGitfBoxInfo;
use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Entity\UserInventoryInfo;
use App\Domain\User\Entity\UserShipInfo;
use App\Domain\User\Entity\UserWeatherHistory;
use App\Domain\User\Repository\UserRepository;
use App\Exception\ErrorCode;
use Psr\Log\LoggerInterface;
use Firebase\JWT\JWT;

class UserService extends BaseService
{
    protected UserRepository $userRepository;
    protected UpgradeRepository $upgradeRepository;
    protected FishingRepository $fishingRepository;
    protected CommonRepository $commonRepository;
    protected LoggerInterface $logger;
    protected ScribeService $scribeService;
    protected RedisService $redisService;

    private const USER_REDIS_KEY = 'user:%s';
    private const WEATHER_REDIS_KEY = 'weather:%s';

    public function __construct(LoggerInterface $logger
        , UserRepository                        $userRepository
        , UpgradeRepository                     $upgradeRepository
        , FishingRepository                     $fishingRepository
        , CommonRepository                      $commonRepository
        , ScribeService                         $scribeService
        , RedisService                          $redisService)
    {
        $this->logger = $logger;
        $this->userRepository = $userRepository;
        $this->upgradeRepository = $upgradeRepository;
        $this->fishingRepository = $fishingRepository;
        $this->commonRepository = $commonRepository;
        $this->scribeService = $scribeService;
        $this->redisService = $redisService;
    }

    public function createUserInfo(array $input): array
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
        $code = new ErrorCode();
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
            //scribe 로그 남기기
            date_default_timezone_set('Asia/Seoul');
            $currentDate = date("Ymd");
            $currentTime = date("Y-m-d H:i:s");

            $dataJson = json_encode([
                "date" => $currentTime,
                "dateTime" => $currentTime,
                "channel_uid" => "0",
                "game" => ScribeService::PROJECT_NAME,
                "server_id" => 'KR',
                "account_id" => $data->decoded->data->accountCode,
                "account_level" => 0,
                "character_id" => $userCode,
                "character_type_id" => 0,
                "character_level" => 1,
                "app_id" => ScribeService::PROJECT_NAME,
                "client_ip" => $_SERVER['REMOTE_ADDR'],
                "server_ip" => $_SERVER['SERVER_ADDR'],
                "channel" => "C2S",
                "company" => "C2S",
                "guid" => $_SERVER['GUID']
            ]);

            $msg[] = new \LogEntry(array(
                'category' => 'uruk_game_character_creation_log_'.$currentDate,
                'message' => $dataJson
            ));
            $this->scribeService->Log($msg);
            $this->logger->info("create user service");

            return [
                'userCode' => $userCode,
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
            ];
        }else{
            return [
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::FAIL_FUNCTION),
            ];
        }
    }

    public function createUserFishingItem(array $input): array
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
        $code = new ErrorCode();
        if($choiceCode > 0){
            return [
                'choiceCode' => $choiceCode,
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS_CREATED),
            ];
        }else{
            return [
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::BAD_REQUEST),
            ];
        }
    }

    public function getUserInfo(array $input): array
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
        $code = new ErrorCode();
        $this->logger->info("get user service");
        return [
            'userInfo' => $userInfo->toJson(),
            'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
        ];
    }

    public function getUserInfoChoice(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $myUserInfo = new UserInfo();
        $myUserInfo->setAccountCode($data->decoded->data->accountCode);
        $myUserInfo->setUserCode($data->userCode);

        $userInfo = $this->userRepository->getUserInfo($myUserInfo);
        $payload = array();

        $code = new ErrorCode();
        if(!empty($userInfo->getUserCode())){
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
            $payload['codeArray'] = $code->getErrorArrayItem(ErrorCode::SUCCESS);

            //scribe 로그 남기기
            date_default_timezone_set('Asia/Seoul');
            $currentDate = date("Ymd");
            $currentTime = date("Y-m-d H:i:s");

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
                "app_id" => ScribeService::PROJECT_NAME,
                "client_ip" => $_SERVER['REMOTE_ADDR'],
                "server_ip" => $_SERVER['SERVER_ADDR'],
                "channel" => "C2S",
                "company" => "C2S",
                "guid" => $_SERVER['GUID']
            ]);

            $msg[] = new \LogEntry(array(
                'category' => 'uruk_game_character_login_log_'.$currentDate,
                'message' => $dataJson
            ));
            $this->scribeService->Log($msg);
            $this->logger->info("get choice user service ");

            return $payload;
        }else{
            return [
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::BAD_REQUEST),
            ];
        }
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
        $code = new ErrorCode();
        if(array_filter($userInfoArray)){
            return [
                'userInfoList' => $userInfoArray,
                'totalCount' => $userInfoArrayCnt,
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
            ];
        }else{
            return [
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::NOT_CONTENTS),
            ];
        }
    }

    public function modifyUserInfo(array $input): array
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
        $code = new ErrorCode();
        $this->logger->info("update user service");
        return [
            'userInfo' => $result,
            'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
        ];
    }

    public function modifyUserWeatherInfo(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
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
        $code = new ErrorCode();
        return [
            'weatherInfo' => $result,
            'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
        ];
    }

    public function getUserWeatherInfo(array $input): array
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

        $code = new ErrorCode();
        $this->logger->info("get user weather service");
        return [
            'weatherInfo' => $weatherInfo,
            'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
        ];
    }

    public function getUserShipInfo(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $myUserShipInfo = new UserShipInfo();
        $myUserShipInfo->setUserCode($data->decoded->data->userCode);

        //캐릭터 보로롱24 정보 조회
        $userShipInfo = $this->userRepository->getUserShipInfo($myUserShipInfo);
        $this->logger->info("get user ship service");
        $code = new ErrorCode();
        return [
        'shipInfo' => $userShipInfo,
            'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
        ];
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

        $code = new ErrorCode();
        $this->logger->info("get list user inventory service");
        if(array_filter($userInventoryArray)){
            return [
                'userInventoryList' => $userInventoryArray,
                'totalCount' => $userInventoryArrayCnt,
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
            ];
        }else{
            return [
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
            ];
        }
    }

    public function getUserInventory(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $myUserInventory = new UserInventoryInfo();
        $myUserInventory->setUserCode($data->decoded->data->userCode);
        $myUserInventory->setInventoryCode($data->InventoryCode);

        //캐릭터 인벤토리 상세 조회
        $userInventory = $this->userRepository->getUserInventory($myUserInventory);
        $code = new ErrorCode();
        $this->logger->info("get user inventory service");
        return [
            'userInventory' => $userInventory,
            'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
        ];
    }

    public function removeUserInfo(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $myUserInfo = new UserInfo();
        if(!empty($data->decoded->data->userCode)){
            $myUserInfo->setUserCode($data->decoded->data->userCode);
        }else{
            $myUserInfo->setUserCode($data->userCode);
        }
        //캐릭터 삭제
        $userInfo = $this->userRepository->getUserInfo($myUserInfo);
        $resultCode = $this->userRepository->deleteUserInfo($myUserInfo);
        $code = new ErrorCode();
        if($resultCode > 0){
            //redis 삭제
            if(self::isRedisEnabled()==true){
                $this->deleteFromCache($myUserInfo->getUserCode(), self::USER_REDIS_KEY);
            }
            //scribe 로그 남기기
            date_default_timezone_set('Asia/Seoul');
            $currentDate = date("Ymd");
            $currentTime = date("Y-m-d H:i:s");

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
                "app_id" => ScribeService::PROJECT_NAME,
                "client_ip" => $_SERVER['REMOTE_ADDR'],
                "server_ip" => $_SERVER['SERVER_ADDR'],
                "channel" => "C2S",
                "company" => "C2S",
                "guid" => $_SERVER['GUID']
            ]);

            $msg[] = new \LogEntry(array(
                'category' => 'uruk_game_character_delete_log_'.$currentDate,
                'message' => $dataJson
            ));
            $this->scribeService->Log($msg);
            $this->logger->info("delete user info service");
            return [
                'deleteCount' => $resultCode,
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
            ];
        }else{
            return [
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::FAIL_FUNCTION),
            ];
        }
    }

    public function removeUserInventory(array $input): array
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
        $code = new ErrorCode();
        if($resultCode>0){
            $this->logger->info("delete user inventory service");
            return [
                'deleteCount' => $resultCode,
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
            ];
        }else{
            return [
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::FAIL_FUNCTION),
            ];
        }
    }

    public function getUserGiftBox(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $myBoxInfo = new UserGitfBoxInfo();
        $myBoxInfo->setUserCode($data->decoded->data->userCode);
        $myBoxInfo->setBoxCode($data->boxCode);

        //캐릭터 선물함(우편함) 상세 조회
        $boxInfo = $this->userRepository->getUserGiftBoxInfo($myBoxInfo);
        $code = new ErrorCode();
        $this->logger->info("get user gift box service");
        if($boxInfo->getUserCode() > 0){
            return [
                'userGiftBoxInfo' => $boxInfo,
                'codeArray' => $code->getErrorArrayItem(ErrorCode::SUCCESS),
            ];
        }else{
            return [
                'codeArray' => $code->getErrorArrayItem(ErrorCode::FAIL_FUNCTION),
            ];
        }
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
        $code = new ErrorCode();
        $this->logger->info("get list user gift box service");
        return [
            'userGiftBoxList' => $boxArray,
            'totalCount' => $boxArrayCnt,
            'codeArray' => $code->getErrorArrayItem(ErrorCode::SUCCESS),
        ];
    }

    public function getUserFishingItem(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $myChoiceInfo = new UserChoiceItemInfo();
        $myChoiceInfo->setUserCode($data->decoded->data->userCode);
        $myChoiceInfo->setChoiceCode($data->choiceCode);

        //캐릭터 채비 상세 조회
        $choiceInfo = $this->userRepository->getUserFishingItem($myChoiceInfo);
        $code = new ErrorCode();
        $this->logger->info("get user fishing item service");
        return [
            'fishingItemInfo' => $choiceInfo,
            'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
        ];
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
        $code = new ErrorCode();
        $this->logger->info("get list user fishing item service");
        return [
            'userFishingItemList' => $fishingItemArray,
            'totalCount' => $fishingItemArrayCnt,
            'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
        ];
    }

    public function modifyUserGiftBox(array $input): array
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
        $code = new ErrorCode();
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
                } elseif ($boxInfo->getItemType() == 1 || $boxInfo->getItemType() == 2
                    || $boxInfo->getItemType() == 5){
                    if($userInfo->getUseInventoryCount() >= ($inventoryCnt+$boxInfo->getItemCount())){
                        //업그레이드 초기값 코드 조회
                        $myUpgrade = new FishingItemUpgradeData();
                        $myUpgrade->setItemGradeCode($boxInfo->getItemCode());
                        $myUpgrade->setItemType($boxInfo->getItemType());
                        $myUpgrade->setUpgradeLevel(1);
                        $upgradeCode = $this->upgradeRepository->getFishingItemUpgradeCode($myUpgrade);

                        $myInventory = new UserInventoryInfo();
                        $myInventory->setUserCode($boxInfo->getUserCode());
                        $myInventory->setItemCode($boxInfo->getItemCode());
                        $myInventory->setItemType($boxInfo->getItemType());
                        $myInventory->setUpgradeCode($upgradeCode);
                        $myInventory->setUpgradeLevel(1);
                        $myInventory->setItemCount(1);
                        if($boxInfo->getItemType() == 1){
                            $originalItem = $this->fishingRepository->getFishingRodGradeData($myInventory);
                        } elseif ($boxInfo->getItemType() == 2){
                            $originalItem = $this->fishingRepository->getFishingLineGradeData($myInventory);
                        } else {
                            $originalItem = $this->fishingRepository->getFishingReelGradeData($myInventory);
                        }
                        $myInventory->setItemDurability($originalItem->getDurability());
                        //인벤토리 등록
                        for($i=0; $i<$boxInfo->getItemCount(); $i++){
                            $this->userRepository->createUserInventoryInfo($myInventory);
                        }
                    }else{
                        return [
                            'codeArray' => $code->getErrorArrayItem(ErrorCode::SUCCESS),
                        ];
                    }
                } else{
                    //인벤토리 여부 확인, 인벤토리 코드 return
                    $search->setItemCode($boxInfo->getItemCode());
                    $search->setItemType($boxInfo->getItemType());
                    $inventoryCode = $this->userRepository->getUserInventoryCode($search);

                    $myInventory = new UserInventoryInfo();
                    $myInventory->setUserCode($boxInfo->getUserCode());
                    if ($inventoryCode != 0){
                        //인벤토리에 같은 아이템 조회
                        $myInventory->setInventoryCode($inventoryCode);
                        $inventoryInfo = $this->userRepository->getUserInventory($myInventory);
                        //인벤토리 등록 (카운트 증가)
                        $inventoryInfo->setItemCount($inventoryInfo->getItemCount()+$boxInfo->getItemCount());
                        $this->userRepository->createUserInventoryInfo($inventoryInfo);
                    }else{
                        if($userInfo->getUseInventoryCount() >= ($inventoryCnt+1)){
                            $myInventory->setItemCode($boxInfo->getItemCode());
                            $myInventory->setItemType($boxInfo->getItemType());
                            $myInventory->setUpgradeCode(0);
                            $myInventory->setUpgradeLevel(0);
                            $myInventory->setItemCount($boxInfo->getItemCount());
                            $myInventory->setItemDurability(1);
                            //인벤토리 등록
                            $this->userRepository->createUserInventoryInfo($myInventory);
                        }else{
                            return [
                                'codeArray' => $code->getErrorArrayItem(ErrorCode::FULL_DATA),
                            ];
                        }
                    }
                }
                //선물함(우편함) 읽음으로 수정
                $this->userRepository->modifyUserGiftBoxStatus($boxInfo);
                if (self::isRedisEnabled() === true) {
                    $user = $this->userRepository->getUserInfo($userInfo);
                    $this->saveInCache($user->getUserCode(), $user, self::USER_REDIS_KEY);
                }
                return [
                    'codeArray' => $code->getErrorArrayItem(ErrorCode::GIFT_SUCCESS),
                ];
            }else{
                return [
                    'codeArray' => $code->getErrorArrayItem(ErrorCode::REDUPLICATION_CONTENTS),
                ];
            }
        }else{
            $search->setItemType(99);
            $itemCount = $this->userRepository->getUserGiftBoxFishingItemSum($search);
            $boxCnt = $this->userRepository->getUserGiftBoxListCnt($search) + $itemCount;

            $myBoxInfo->setBoxCode(0);
            if($boxCnt != 0){
                if($userInfo->getUseInventoryCount() >= ($inventoryCnt+$boxCnt)){
                    //캐릭터 재화 추가
                    $this->userRepository->modifyUserInfoGiftBox($myBoxInfo);

                    //재화를 제외한 선물 아이템 조회
                    $boxArray = $this->userRepository->getUserGiftBoxs($myBoxInfo);
                    //인벤토리 등록
                    for($i = 0; $i < count($boxArray); $i++){
                        $myInventory = new UserInventoryInfo();
                        $myInventory->setUserCode($data->decoded->data->userCode);

                        if($boxArray[$i]['itemType'] == 1 || $boxArray[$i]['itemType'] == 2 || $boxArray[$i]['itemType'] == 5){
                            //업그레이드 초기값 코드 조회
                            $myUpgrade = new FishingItemUpgradeData();
                            $myUpgrade->setItemGradeCode($boxArray[$i]['itemCode']);
                            $myUpgrade->setItemType($boxArray[$i]['itemType']);
                            $myUpgrade->setUpgradeLevel(1);
                            $upgradeCode = $this->upgradeRepository->getFishingItemUpgradeCode($myUpgrade);

                            $myInventory->setItemCode($boxArray[$i]['itemCode']);
                            $myInventory->setItemType($boxArray[$i]['itemType']);
                            $myInventory->setUpgradeCode($upgradeCode);
                            $myInventory->setUpgradeLevel(1);
                            $myInventory->setItemCount(1);
                            if($boxArray[$i]['itemType'] == 1){
                                $originalItem = $this->fishingRepository->getFishingRodGradeData($myInventory);
                            } elseif ($boxArray[$i]['itemType'] == 2){
                                $originalItem = $this->fishingRepository->getFishingLineGradeData($myInventory);
                            } else {
                                $originalItem = $this->fishingRepository->getFishingReelGradeData($myInventory);
                            }
                            $myInventory->setItemDurability($originalItem->getDurability());
                            //인벤토리 등록
                            for($j=0; $j<$boxArray[$i]['itemCount']; $j++){
                                $this->userRepository->createUserInventoryInfo($myInventory);
                            }
                        }else{
                            //인벤토리 여부 확인, 인벤토리 코드 return
                            $search->setItemCode($boxArray[$i]['itemCode']);
                            $search->setItemType($boxArray[$i]['itemType']);
                            $inventoryCode = $this->userRepository->getUserInventoryCode($search);

                            if ($inventoryCode != 0){
                                //인벤토리에 같은 아이템 조회
                                $myInventory->setInventoryCode($inventoryCode);
                                $inventoryInfo = $this->userRepository->getUserInventory($myInventory);
                                //인벤토리 등록 (카운트 증가)
                                $inventoryInfo->setItemCount($inventoryInfo->getItemCount()+$boxArray[$i]['itemCount']);
                                $this->userRepository->createUserInventoryInfo($inventoryInfo);
                            }else{
                                $myInventory->setItemCode($boxArray[$i]['itemCode']);
                                $myInventory->setItemType($boxArray[$i]['itemType']);
                                $myInventory->setUpgradeCode(0);
                                $myInventory->setUpgradeLevel(0);
                                $myInventory->setItemCount($boxArray[$i]['itemCount']);
                                $myInventory->setItemDurability(1);
                                //인벤토리 등록
                                $this->userRepository->createUserInventoryInfo($myInventory);
                            }
                        }
                    }
                    //현재는 안씀.
                    //$this->userRepository->createUserGiftBoxToInventory($myBoxInfo);
                    //선물함(우편함) 읽음으로 수정
                    $this->userRepository->modifyUserGiftBoxStatus($myBoxInfo);
                    if (self::isRedisEnabled() === true) {
                        $user = $this->userRepository->getUserInfo($userInfo);
                        $this->saveInCache($user->getUserCode(), $user, self::USER_REDIS_KEY);
                    }
                    return [
                        'codeArray' => $code->getErrorArrayItem(ErrorCode::GIFT_SUCCESS),
                    ];
                }else{
                    return [
                        'codeArray' => $code->getErrorArrayItem(ErrorCode::FULL_DATA),
                    ];
                }
            }else{
                return [
                    'codeArray' => $code->getErrorArrayItem(ErrorCode::REDUPLICATION_CONTENTS),
                ];
            }
        }
    }

    public function deleteUserGiftBox(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $myBoxInfo = new UserGitfBoxInfo();
        $myBoxInfo->setUserCode($data->decoded->data->userCode);
        if(!empty($data->boxCode)){
            $myBoxInfo->setBoxCode($data->boxCode);
        }else{
            $myBoxInfo->setBoxCode(0);
        }
        $resultCode = $this->userRepository->deleteUserGiftBox($myBoxInfo);
        $code = new ErrorCode();
        $this->logger->info("delete user giftBox item service");
        return [
            'deleteCount' => $resultCode,
            'codeArray' => $code->getErrorArrayItem(ErrorCode::SUCCESS),
        ];
    }

    public function modifyUserFishingItem(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $myChoiceItem = new UserChoiceItemInfo();
        $myChoiceItem->setUserCode($data->decoded->data->userCode);
        $myChoiceItem->setChoiceCode($data->choiceCode);
        $choiceInfo = $this->userRepository->getUserFishingItem($myChoiceItem);

        if(!empty($data->fishingRodCode)){
            $choiceInfo->setFishingRodCode($data->fishingRodCode);
        }
        if(!empty($data->fishingLineCode)){
            $choiceInfo->setFishingLineCode($data->fishingLineCode);
        }
        if(!empty($data->fishingNeedleCode)){
            $choiceInfo->setFishingNeedleCode($data->fishingNeedleCode);
        }
        if(!empty($data->fishingBaitCode)){
            $choiceInfo->setFishingBaitCode($data->fishingBaitCode);
        }
        if(!empty($data->fishingReelCode)){
            $choiceInfo->setFishingReelCode($data->fishingReelCode);
        }
        if(!empty($data->fishingItemCode1)){
            $choiceInfo->setFishingItemCode1($data->fishingItemCode1);
        }
        if(!empty($data->fishingItemCode2)){
            $choiceInfo->setFishingItemCode2($data->fishingItemCode2);
        }
        if(!empty($data->fishingItemCode3)){
            $choiceInfo->setFishingItemCode3($data->fishingItemCode3);
        }
        if(!empty($data->fishingItemCode4)){
            $choiceInfo->setFishingItemCode4($data->fishingItemCode4);
        }

        //채비 수정
        $this->userRepository->createUserFishingItem($choiceInfo);
        $code = new ErrorCode();
        $this->logger->info("update user fishing-item Service");
        return [
            'fishingItemInfo' => $choiceInfo,
            'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
        ];
    }

    public function deleteUserFishingItem(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $myChoiceItem = new UserChoiceItemInfo();
        $myChoiceItem->setUserCode($data->decoded->data->userCode);
        $myChoiceItem->setChoiceCode($data->choiceCode);
        $this->userRepository->deleteUserFishingItem($myChoiceItem);
        $code = new ErrorCode();
        $this->logger->info("delete user fishing-item service");
        return [
            'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
        ];
    }

    public function getUserFishDictionary(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $myDictionary = new UserFishDictionary();
        $myDictionary->setUserCode($data->decoded->data->userCode);
        $myDictionary->setMapFishCode($data->mapFishCode);
        $dictionary = $this->userRepository->getUserFishDictionaryInfo($myDictionary);
        $code = new ErrorCode();
        $this->logger->info("get user fish dictionary service");
        return [
            'fishDictionary' => $dictionary,
            'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
        ];
    }

    public function getUserFishDictionaryList(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $search = new SearchInfo();
        $search->setUserCode($data->decoded->data->userCode);
        $search->setLimit($data->limit);
        $search->setOffset($data->offset);

        $dictionaryArray = $this->userRepository->getUserFishDictionaryList($search);
        $dictionaryArrayCnt = $this->userRepository->getUserFishDictionaryListCnt($search);
        $code = new ErrorCode();
        $this->logger->info("get list user fish dictionary service");
        return [
            'fishDictionaryList' => $dictionaryArray,
            'totalCount' => $dictionaryArrayCnt,
            'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
        ];
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