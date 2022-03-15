<?php

namespace App\Domain\Map\Service;

use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\Map\Entity\MapInfoData;
use App\Domain\Map\Entity\MapTideData;
use App\Domain\Map\Repository\MapRepository;
use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Entity\UserShipInfo;
use App\Domain\User\Entity\UserWeatherHistory;
use App\Domain\User\Repository\UserRepository;
use Psr\Log\LoggerInterface;

class MapService extends BaseService
{
    protected MapRepository $mapRepository;
    protected UserRepository $userRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

    private const MAP_REDIS_KEY = 'map:%s';
    private const TIDE_REDIS_KEY = 'mapTide:%s';
    private const WEATHER_REDIS_KEY = 'weather:%s';

    public function __construct(LoggerInterface $logger
        ,MapRepository $mapRepository
        ,UserRepository $userRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->mapRepository = $mapRepository;
        $this->userRepository = $userRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }

    public function getMapInfo(array $input): MapInfoData
    {
        $data = json_decode((string) json_encode($input), false);
        $myMapInfo = new MapInfoData();
        $myMapInfo->setMapCode($data->mapCode);

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
        $myUserInfo = new UserInfo();
        $myUserInfo->setUserCode($data->decoded->data->userCode);
        $userInfo = $this->userRepository->getUserInfo($myUserInfo);

        $myShipInfo = new UserShipInfo();
        $myShipInfo->setUserCode($data->decoded->data->userCode);
        $shipInfo = $this->userRepository->getUserShipInfo($myShipInfo);

        if(self::isRedisEnabled() === true){
            $mapInfo = $this->getOneMapCache($data->mapCode, self::MAP_REDIS_KEY);
            $mapTideInfo = $this->getOneMapTideCache($data->mapCode, self::TIDE_REDIS_KEY);
            $weatherInfo = $this->getOneWeatherCache($data->decoded->data->weatherHistoryCode, self::WEATHER_REDIS_KEY);
        }else{
            $myMapInfo = new MapInfoData();
            $myMapInfo->setMapCode($data->mapCode);
            $mapInfo = $this->mapRepository->getMapInfo($myMapInfo);

            $myMapInfo = new MapTideData();
            $myMapInfo->setMapCode($data->mapCode);
            $mapTideInfo = $this->mapRepository->getMapTideInfo($myMapInfo);

            $myWeatherInfo = new UserWeatherHistory();
            $myWeatherInfo->setUserCode($data->decoded->data->userCode);
            $weatherInfo = $this->userRepository->getUserWeatherHistory($myWeatherInfo);
        }

        $this->logger->info("map leve port service");
        if($userInfo->getFatigue() >= $mapInfo->getDeparturePrice()
            && $shipInfo->getDurability() >= $mapInfo->getDepartureTime() * $mapInfo->getPerDurability()
            && $shipInfo->getFuel() >= $mapInfo->getDistance()*2){

            $userInfo->setFatigue($userInfo->getFatigue()-$mapInfo->getDeparturePrice());
            $this->userRepository->modifyUserInfo($userInfo);
            $userInfo = $this->userRepository->getUserInfo($userInfo);

            $myShipInfo->setDurability($shipInfo->getDurability() - ($mapInfo->getDepartureTime() * $mapInfo->getPerDurability()));
            $myShipInfo->setFuel($shipInfo->getFuel() - ($mapInfo->getDistance()*2));
            $this->userRepository->modifyUserShip($myShipInfo);
            $shipInfo = $this->userRepository->getUserShipInfo($myShipInfo);

            $weatherInfo->setMapCode($mapInfo->getMapCode());
            $this->userRepository->createUserWeather($weatherInfo);
            if (self::isRedisEnabled() === true) {
                $this->saveInCache($data->decoded->data->weatherHistoryCode, $weatherInfo, self::WEATHER_REDIS_KEY);
            }
            return [
                'mapInfo' => $mapInfo,
                'mapTideInfo' => $mapTideInfo,
                'userInfo' => $userInfo,
                'shipInfo' => $shipInfo,
            ];
        }else{
            return [];
        }
    }

    public function modifyShipDurability(array $input):UserShipInfo
    {
        $data = json_decode((string) json_encode($input), false);
        $myShipInfo = new UserShipInfo();
        $myShipInfo->setUserCode($data->decoded->data->userCode);
        $shipInfo = $this->userRepository->getUserShipInfo($myShipInfo);

        if(self::isRedisEnabled() === true){
            $mapInfo = $this->getOneMapCache($data->mapCode, self::MAP_REDIS_KEY);
        }else{
            $myMapInfo = new MapInfoData();
            $myMapInfo->setMapCode($data->mapCode);
            $mapInfo = $this->mapRepository->getMapInfo($myMapInfo);
        }

        $myWeatherInfo = new UserWeatherHistory();
        $myWeatherInfo->setUserCode($data->decoded->data->userCode);
        $weatherInfo = $this->userRepository->getUserWeatherHistory($myWeatherInfo);

        date_default_timezone_set('Asia/Seoul');
        $currentTime = date("Y-m-d H:i:s");
        $timeDif = strtotime($currentTime) - strtotime($weatherInfo->getMapUpdateDate());
        $perMinute = ceil($timeDif / (60));

        $this->logger->info("map ship durability service ".$perMinute);
        if($shipInfo->getDurability() >= $perMinute * $mapInfo->getPerDurability()){
            $shipInfo->setDurability($shipInfo->getDurability() - ($perMinute * $mapInfo->getPerDurability()));
            $this->userRepository->modifyUserShip($shipInfo);

            $myWeatherInfo = new UserWeatherHistory();
            $myWeatherInfo->setUserCode($data->decoded->data->userCode);
            $weatherInfo = $this->userRepository->getUserWeatherHistory($myWeatherInfo);
            $weatherInfo->setMapCode($mapInfo->getMapCode());
            $this->userRepository->createUserWeather($weatherInfo);

            return $this->userRepository->getUserShipInfo($myShipInfo);
        }else{
            return new UserShipInfo();
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