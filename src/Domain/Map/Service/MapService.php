<?php

namespace App\Domain\Map\Service;

use App\Domain\Auction\Repository\AuctionRepository;
use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\Fishing\Repository\FishingRepository;
use App\Domain\Map\Entity\MapInfoData;
use App\Domain\Map\Entity\MapTideData;
use App\Domain\Map\Repository\MapRepository;
use App\Domain\Quest\Entity\QuestInfoData;
use App\Domain\Quest\Repository\QuestRepository;
use App\Domain\User\Entity\UserFishInventoryInfo;
use App\Domain\User\Entity\UserGitfBoxInfo;
use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Entity\UserQuestInfo;
use App\Domain\User\Entity\UserShipInfo;
use App\Domain\User\Entity\UserWeatherHistory;
use App\Domain\User\Repository\UserRepository;
use Psr\Log\LoggerInterface;

class MapService extends BaseService
{
    protected MapRepository $mapRepository;
    protected UserRepository $userRepository;
    protected CommonRepository $commonRepository;
    protected AuctionRepository $auctionRepository;
    protected FishingRepository $fishingRepository;
    protected QuestRepository $questRepository;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

    private const MAP_REDIS_KEY = 'map:%s';
    private const TIDE_REDIS_KEY = 'mapTide:%s';
    private const WEATHER_REDIS_KEY = 'weather:%s';
    private const USER_REDIS_KEY = 'user:%s';

    public function __construct(LoggerInterface $logger
        ,MapRepository $mapRepository
        ,UserRepository $userRepository
        ,AuctionRepository $auctionRepository
        ,FishingRepository $fishingRepository
        ,QuestRepository $questRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->mapRepository = $mapRepository;
        $this->userRepository = $userRepository;
        $this->auctionRepository = $auctionRepository;
        $this->fishingRepository = $fishingRepository;
        $this->questRepository = $questRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }

    public function getMapInfo(array $input): MapInfoData
    {
        $data = json_decode((string) json_encode($input), false);
        $myMapInfo = new MapInfoData();
        $myMapInfo->setMapCode($data->mapCode);

        //지역 상세 조회
        $mapInfo = $this->mapRepository->getMapInfo($myMapInfo) ;
        $this->logger->info("map info service");
        return $mapInfo;
    }

    public function getMapInfoList(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $search = new SearchInfo();
        $search->setLimit($data->limit);
        $search->setOffset($data->offset);

        //지역 목록 조회
        $mapArray = $this->mapRepository->getMapInfoList($search);
        $mapArrayCnt = $this->mapRepository->getMapInfoListCnt($search);

        $this->logger->info("map info list service");
        return [
            'mapInfoList' => $mapArray,
            'totalCount' => $mapArrayCnt,
        ];
    }

    public function mapLevePort(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $myShipInfo = new UserShipInfo();
        $myShipInfo->setUserCode($data->decoded->data->userCode);
        $shipInfo = $this->userRepository->getUserShipInfo($myShipInfo);

        if(self::isRedisEnabled() === true){
            $mapInfo = $this->getOneMapCache($data->mapCode, self::MAP_REDIS_KEY);
            $mapTideInfo = $this->getOneMapTideCache($data->mapCode, self::TIDE_REDIS_KEY);
            $weatherInfo = $this->getOneWeatherCache($data->decoded->data->weatherHistoryCode, $data->decoded->data->userCode,self::WEATHER_REDIS_KEY);
            $userInfo = $this->getOneUserCache($data->decoded->data->userCode, self::USER_REDIS_KEY);
        }else{
            $myMapInfo = new MapInfoData();
            $myMapInfo->setMapCode($data->mapCode);
            $mapInfo = $this->mapRepository->getMapInfo($myMapInfo);

            $myMapTideInfo = new MapTideData();
            $myMapTideInfo->setMapCode($data->mapCode);
            $myMapTideInfo->setTideSort(1);
            $mapTideInfo = $this->mapRepository->getMapTideInfo($myMapTideInfo);

            $myWeatherInfo = new UserWeatherHistory();
            $myWeatherInfo->setUserCode($data->decoded->data->userCode);
            $weatherInfo = $this->userRepository->getUserWeatherHistory($myWeatherInfo);

            $myUserInfo = new UserInfo();
            $myUserInfo->setUserCode($data->decoded->data->userCode);
            $userInfo = $this->userRepository->getUserInfo($myUserInfo);
        }

        $this->logger->info("map leve port service");
        //출항 가능한 최소 레벨 충족 여부 비교
        if($userInfo->getLevelCode() >= $mapInfo->getMinLevel()){
            if($userInfo->getFatigue() >= $mapInfo->getDeparturePrice()
                && $shipInfo->getDurability() >= $mapInfo->getDepartureTime() * $mapInfo->getPerDurability()
                && $shipInfo->getFuel() >= $mapInfo->getDistance()*2){

                //캐릭터 피로도 감소
                $userInfo->setFatigue($userInfo->getFatigue()-$mapInfo->getDeparturePrice());
                $this->userRepository->modifyUserInfo($userInfo);
                $userInfo = $this->userRepository->getUserInfo($userInfo);

                //보로롱24 내구도, 피로 감소 (풍량의 정상범위에 따라 내구도 연로 추가 감소 로직X) 
                $myShipInfo->setDurability($shipInfo->getDurability() - ($mapInfo->getDepartureTime() * $mapInfo->getPerDurability()));
                $myShipInfo->setFuel($shipInfo->getFuel() - ($mapInfo->getDistance()*2));
                $this->userRepository->modifyUserShip($myShipInfo);
                $shipInfo = $this->userRepository->getUserShipInfo($myShipInfo);

                //날씨 히스토리 정보 수정
                $weatherInfo->setMapCode($mapInfo->getMapCode());
                $weatherInfo->setWindUpdateDate("");
                $weatherInfo->setTemperatureUpdateDate("");
                $this->userRepository->createUserWeather($weatherInfo);
                if (self::isRedisEnabled() === true) {
                    $weatherInfo = $this->userRepository->getUserWeatherHistory($weatherInfo);
                    $this->saveInCache($data->decoded->data->weatherHistoryCode, $weatherInfo, self::WEATHER_REDIS_KEY);
                    $this->saveInCache($data->decoded->data->userCode, $userInfo, self::USER_REDIS_KEY);
                }
                return [
                    'mapInfo' => $mapInfo,
                    'mapTideInfo' => $mapTideInfo,
                    'userInfo' => $userInfo,
                    'shipInfo' => $shipInfo,
                    'message' => $mapInfo->getMapName()."으로 출항했습니다.",
                ];
            }else{
                return [
                    'mapInfo' => $mapInfo,
                    'mapTideInfo' => $mapTideInfo,
                    'userInfo' => $userInfo,
                    'shipInfo' => $shipInfo,
                    'message' => '보로롱24의 내구도와 연로 혹은 피로도를 확인해주세요.'
                ];
            }
        }else{
            return [
                'mapInfo' => $mapInfo,
                'mapTideInfo' => $mapTideInfo,
                'userInfo' => $userInfo,
                'shipInfo' => $shipInfo,
                'message' => '최소레벨을 충족하지 못해서 입장할 수 없습니다.'
            ];
        }
    }

    public function mapEnterPort(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $myShipInfo = new UserShipInfo();
        $myShipInfo->setUserCode($data->decoded->data->userCode);
        $shipInfo = $this->userRepository->getUserShipInfo($myShipInfo);

        if(self::isRedisEnabled() === true){
            $weatherInfo = $this->getOneWeatherCache($data->decoded->data->weatherHistoryCode, $data->decoded->data->userCode,self::WEATHER_REDIS_KEY);
            $userInfo = $this->getOneUserCache($data->decoded->data->userCode, self::USER_REDIS_KEY);
            $mapInfo = $this->getOneMapCache($weatherInfo->getMapCode(), self::MAP_REDIS_KEY);
            $mapTideInfo = $this->getOneMapTideCache($weatherInfo->getMapCode(), self::TIDE_REDIS_KEY);
        }else{
            $myWeatherInfo = new UserWeatherHistory();
            $myWeatherInfo->setUserCode($data->decoded->data->userCode);
            $weatherInfo = $this->userRepository->getUserWeatherHistory($myWeatherInfo);

            $myUserInfo = new UserInfo();
            $myUserInfo->setUserCode($data->decoded->data->userCode);
            $userInfo = $this->userRepository->getUserInfo($myUserInfo);

            $myMapInfo = new MapInfoData();
            $myMapInfo->setMapCode($weatherInfo->getMapCode());
            $mapInfo = $this->mapRepository->getMapInfo($myMapInfo);

            $myMapTideInfo = new MapTideData();
            $myMapTideInfo->setMapCode($weatherInfo->getMapCode());
            $myMapTideInfo->setTideSort(1);
            $mapTideInfo = $this->mapRepository->getMapTideInfo($myMapTideInfo);
        }

        date_default_timezone_set('Asia/Seoul');
        $currentTime = date("Y-m-d H:i:s");
        $timeDif = strtotime($currentTime) - strtotime($weatherInfo->getMapUpdateDate());
        $perMinute = floor($timeDif / (60));

        if($shipInfo->getDurability() >= $perMinute * $mapInfo->getPerDurability()){
            $shipInfo->setDurability($shipInfo->getDurability() - ($perMinute * $mapInfo->getPerDurability()));
        }else{
            $shipInfo->setDurability(0);
        }
        $this->userRepository->modifyUserShip($shipInfo);

        //날짜 히스토리 수정
        $weatherInfo->setMapCode(0);
        $weatherInfo->setWindUpdateDate("");
        $weatherInfo->setTemperatureUpdateDate("");
        $this->userRepository->createUserWeather($weatherInfo);

        //물고기 인벤토리 등록
        $userFishInventory = new UserFishInventoryInfo();
        $userFishInventory->setUserCode($data->decoded->data->userCode);
        $userFishInventory->setMapCode($mapInfo->getMapCode());
        $this->userRepository->createUserInventoryFish($userFishInventory);

        //경매 등록
        $this->auctionRepository->createAuctionInfo($userFishInventory);

        //도감 등록
        $this->userRepository->createUserFishDictionary($userFishInventory);
        //도감 카운트
        $search = new SearchInfo();
        $search->setUserCode($data->decoded->data->userCode);
        $search->setItemCode($mapInfo->getMapCode());
        $dictionaryCnt = $this->userRepository->getUserFishDictionaryCnt($search);

        //지역 퀘스트 상세 조회
        $myQuestInfo = new QuestInfoData();
        $myQuestInfo->setQuestType(2);
        $myQuestInfo->setQuestGoal($mapInfo->getMapCode());
        $questInfo = $this->questRepository->getQuestInfoGoal($myQuestInfo);
        //캐릭터 퀘스트 여부
        $userQuestInfo = new UserQuestInfo();
        $userQuestInfo->setUserCode($userInfo->getUserCode());
        $userQuestInfo->setQuestCode($questInfo->getQuestCode());
        $userQuestCnt = $this->userRepository->getUserQuestInfoCnt($userQuestInfo);

        if($dictionaryCnt == $mapInfo->getMapFishCount() && $userQuestCnt == 0){
            //선물함(우편함) 등록
            $boxInfo = new UserGitfBoxInfo();
            $boxInfo->setUserCode($userInfo->getUserCode());
            $boxInfo->setQuestType(2);
            $boxInfo->setQuestGoal($mapInfo->getMapCode());
            $this->userRepository->createUserGiftBox($boxInfo);

            //캐릭터 퀘스트 등록
            $this->userRepository->createUserQuestInfo($userQuestInfo);
        }

        if (self::isRedisEnabled() === true) {
            $weatherInfo = $this->userRepository->getUserWeatherHistory($weatherInfo);
            $this->saveInCache($data->decoded->data->weatherHistoryCode, $weatherInfo, self::WEATHER_REDIS_KEY);
        }

        //임시 물고기 인벤토리 삭제
        $this->fishingRepository->deleteUserFishInventory($userFishInventory);

        $this->logger->info("map enter port service");
        return [
            'mapInfo' => $mapInfo,
            'mapTideInfo' => $mapTideInfo,
            'userInfo' => $userInfo,
            'shipInfo' => $shipInfo,
            'message' => "항구로 입항했습니다.",
        ];
    }

    public function modifyShipDurability(array $input):UserShipInfo
    {
        $data = json_decode((string) json_encode($input), false);
        $myShipInfo = new UserShipInfo();
        $myShipInfo->setUserCode($data->decoded->data->userCode);
        $shipInfo = $this->userRepository->getUserShipInfo($myShipInfo);

        if(self::isRedisEnabled() === true){
            $mapInfo = $this->getOneMapCache($data->mapCode, self::MAP_REDIS_KEY);
            $weatherInfo = $this->getOneWeatherCache($data->decoded->data->weatherHistoryCode, $data->decoded->data->userCode,self::WEATHER_REDIS_KEY);
        }else{
            $myMapInfo = new MapInfoData();
            $myMapInfo->setMapCode($data->mapCode);
            $mapInfo = $this->mapRepository->getMapInfo($myMapInfo);

            $myWeatherInfo = new UserWeatherHistory();
            $myWeatherInfo->setUserCode($data->decoded->data->userCode);
            $weatherInfo = $this->userRepository->getUserWeatherHistory($myWeatherInfo);
        }

        //분당 보로롱24 내구도 감소
        date_default_timezone_set('Asia/Seoul');
        $currentTime = date("Y-m-d H:i:s");
        $timeDif = strtotime($currentTime) - strtotime($weatherInfo->getMapUpdateDate());
        $perMinute = floor($timeDif / (60));

        $this->logger->info("map ship durability service");
        if($shipInfo->getDurability() >= $perMinute * $mapInfo->getPerDurability()){
            $shipInfo->setDurability($shipInfo->getDurability() - ($perMinute * $mapInfo->getPerDurability()));
            $this->userRepository->modifyUserShip($shipInfo);

            $weatherInfo->setMapCode($mapInfo->getMapCode());
            $weatherInfo->setWindUpdateDate("");
            $weatherInfo->setTemperatureUpdateDate("");
            $this->userRepository->createUserWeather($weatherInfo);

            if (self::isRedisEnabled() === true) {
                $weatherInfo = $this->userRepository->getUserWeatherHistory($weatherInfo);
                $this->saveInCache($data->decoded->data->weatherHistoryCode, $weatherInfo, self::WEATHER_REDIS_KEY);
            }

            return $this->userRepository->getUserShipInfo($myShipInfo);
        }else{
            return $shipInfo;
        }
    }

    protected function getOneMapCache(int $code, string $redisKeys): MapInfoData
    {
        $redisKey = sprintf($redisKeys, $code);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $model = json_decode((string) json_encode($this->redisService->get($key)), false);

            $mapInfo = new MapInfoData();
            $mapInfo->setMapCode($model->mapCode);
            $mapInfo->setMapName($model->mapName);
            $mapInfo->setMaxDepth($model->maxDepth);
            $mapInfo->setMinDepth($model->minDepth);
            $mapInfo->setMinLevel($model->minLevel);
            $mapInfo->setDistance($model->distance);
            $mapInfo->setMoneyCode($model->moneyCode);
            $mapInfo->setDeparturePrice($model->departurePrice);
            $mapInfo->setDepartureTime($model->departureTime);
            $mapInfo->setPerDurability($model->perDurability);
            $mapInfo->setMapFishCount($model->mapFishCount);
            $mapInfo->setFishSizeProbability($model->fishSizeProbability);
            $mapInfo->setCreateDate($model->createDate);
        }else{
            $myMapInfo = new MapInfoData();
            $myMapInfo->setMapCode($code);
            $mapInfo = $this->mapRepository->getMapInfo($myMapInfo);

            $search = new SearchInfo();
            $mapArray = $this->mapRepository->getMapInfoList($search);
            for ($i=0; $i<count($mapArray); $i++){
                $data = json_decode((string) json_encode($mapArray[$i]), false);
                $this->saveInCache($data->mapCode, $data, $redisKeys);
            }
        }
        return $mapInfo;
    }

    protected function getOneMapTideCache(int $code, string $redisKeys): MapTideData
    {
        $redisKey = sprintf($redisKeys, $code);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $model = json_decode((string) json_encode($this->redisService->get($key)), false);
            date_default_timezone_set('Asia/Seoul');
            $currentDate = date("Y-m-d");
            $createDate = date("Y-m-d", strtotime($model->createDate));
            if(strtotime($currentDate) != strtotime($createDate)){
                $search = new SearchInfo();
                if($model->tideSort == 7){
                    $search->setSort(1);
                }else{
                    $search->setSort($model->tideSort + 1);
                }
                $mapArray = $this->mapRepository->getMapTideList($search);
                for ($i=0; $i<count($mapArray); $i++){
                    $data = json_decode((string) json_encode($mapArray[$i]), false);
                    $this->saveInCache($data->mapCode, $data, $redisKeys);
                }
                $this->logger->info("map tide update");
            }

            $mapTideInfo = new MapTideData();
            $mapTideInfo->setMapTideCode($model->mapTideCode);
            $mapTideInfo->setMapCode($model->mapCode);
            $mapTideInfo->setTideCode($model->tideCode);
            $mapTideInfo->setTideSort($model->tideSort);
            $mapTideInfo->setHighTideTime1($model->highTideTime1);
            $mapTideInfo->setLowTideTime1($model->lowTideTime1);
            $mapTideInfo->setHighTideTime2($model->highTideTime2);
            $mapTideInfo->setLowTideTime2($model->lowTideTime2);
            $mapTideInfo->setWaterSplashTime($model->waterSplashTime);
            $mapTideInfo->setAppearProbability($model->appearProbability);
            $mapTideInfo->setCreateDate($model->createDate);
        }else{
            $myMapInfo = new MapTideData();
            $myMapInfo->setMapCode($code);
            $myMapInfo->setSort(1);
            $mapTideInfo = $this->mapRepository->getMapTideInfo($myMapInfo);

            $search = new SearchInfo();
            $search->setSort(1);
            $mapArray = $this->mapRepository->getMapTideList($search);
            for ($i=0; $i<count($mapArray); $i++){
                $data = json_decode((string) json_encode($mapArray[$i]), false);
                $this->saveInCache($data->mapCode, $data, $redisKeys);
            }
        }
        return $mapTideInfo;
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
}