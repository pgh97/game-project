<?php

namespace App\Domain\Fishing\Service;

use App\Domain\Common\Entity\fish\fishGradeData;
use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Common\Entity\Weather\WeatherInfoData;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\Fishing\Repository\FishingRepository;
use App\Domain\Map\Entity\MapInfoData;
use App\Domain\Map\Entity\MapTideData;
use App\Domain\Map\Repository\MapRepository;
use App\Domain\User\Entity\UserFishInventoryInfo;
use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Entity\UserWeatherHistory;
use App\Domain\User\Repository\UserRepository;
use Psr\Log\LoggerInterface;

class FishingService extends BaseService
{
    protected FishingRepository $fishingRepository;
    protected UserRepository $userRepository;
    protected MapRepository $mapRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

    private const FISHING_REDIS_KEY = 'fishing:%s';
    private const USER_REDIS_KEY = 'user:%s';
    private const WEATHER_REDIS_KEY = 'weather:%s';
    private const MAP_REDIS_KEY = 'map:%s';
    private const TIDE_REDIS_KEY = 'mapTide:%s';

    public function __construct(LoggerInterface $logger
        ,FishingRepository $fishingRepository
        ,UserRepository $userRepository
        ,MapRepository $mapRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->fishingRepository = $fishingRepository;
        $this->userRepository = $userRepository;
        $this->mapRepository = $mapRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }

    public function getFishInventory(array $input):UserFishInventoryInfo
    {
        $data = json_decode((string) json_encode($input), false);
        $myFishInventory = new UserFishInventoryInfo();
        $myFishInventory->setUserCode($data->decoded->data->accountCode);
        $myFishInventory->setFishInventoryCode($data->fishInventoryCode);

        $fishInventory = $this->fishingRepository->getUserFishInventory($myFishInventory);
        $this->logger->info("get fish inventory info service");
        return $fishInventory;
    }

    public function getFishInventoryList(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        $search = new SearchInfo();
        $search->setUserCode($data->decoded->data->userCode);
        $search->setItemCode($data->itemCode);
        $search->setLimit($data->limit);
        $search->setOffset($data->offset);

        $fishInventoryArray = $this->fishingRepository->getUserFishInventoryList($search);
        $fishInventoryArrayCnt = $this->fishingRepository->getUserFishInventoryListCnt($search);
        $this->logger->info("get list fish inventory info service");
        return [
            'fishInventoryList' => $fishInventoryArray,
            'totalCount' => $fishInventoryArrayCnt,
        ];
    }

    public function fishingOperate(array $input):object
    {
        $data = json_decode((string) json_encode($input), false);
        if(self::isRedisEnabled() === true){
            $userInfo = $this->getOneUserCache($data->decoded->data->userCode, self::USER_REDIS_KEY);
            $weatherInfo = new UserWeatherHistory();
            $weatherInfo = $this->getOneWeatherCache($data->decoded->data->weatherHistoryCode, self::WEATHER_REDIS_KEY);
            $mapInfo = $this->getOneMapCache($data->mapCode, self::MAP_REDIS_KEY);
            $mapTideInfo = $this->getOneMapTideCache($data->mapCode, self::TIDE_REDIS_KEY);
        }else{
            $myUserInfo = new UserInfo();
            $myUserInfo->setUserCode($data->decoded->data->userCode);
            $userInfo = $this->userRepository->getUserInfo($myUserInfo);

            $myWeatherInfo= new UserWeatherHistory();
            $myWeatherInfo->setUserCode($data->decoded->data->userCode);
            $weatherInfo = $this->userRepository->getUserWeatherHistory($myWeatherInfo);

            $myMapInfo = new MapInfoData();
            $myMapInfo->setMapCode($data->mapCode);
            $mapInfo = $this->mapRepository->getMapInfo($myMapInfo);

            $myTideInfo = new MapTideData();
            $myTideInfo->setMapCode($data->mapCode);
            $mapTideInfo = $this->mapRepository->getMapTideInfo($myTideInfo);
       }

        date_default_timezone_set('Asia/Seoul');
        $currentDate = date("Y-m-d");

        return $weatherInfo;
    }

    protected function getOneUserCache(int $code, string $redisKeys): object
    {
        $redisKey = sprintf($redisKeys, $code);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $userInfo = json_decode((string)json_encode($this->redisService->get($key)), false);
        } else {
            $myUserInfo = new UserInfo();
            $myUserInfo->setUserCode($code);
            $userInfo = $this->userRepository->getUserInfo($myUserInfo);
        }
        return $userInfo;
    }

    protected function getOneWeatherCache(int $code, string $redisKeys): object
    {
        $redisKey = sprintf($redisKeys, $code);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $weatherInfo = json_decode((string)json_encode($this->redisService->get($key)), false);
        } else {
            $myWeatherInfo = new UserWeatherHistory();
            $myWeatherInfo->setUserCode($code);
            $weatherInfo = $this->userRepository->getUserWeatherHistory($myWeatherInfo);
        }
        return $weatherInfo;
    }

    protected function getOneMapCache(int $code, string $redisKeys): object
    {
        $redisKey = sprintf($redisKeys, $code);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $mapInfo = json_decode((string) json_encode($this->redisService->get($key)), false);
        }else{
            $myMapInfo = new MapInfoData();
            $myMapInfo->setMapCode($code);
            $mapInfo = $this->mapRepository->getMapInfo($myMapInfo);
        }
        return $mapInfo;
    }

    protected function getOneMapTideCache(int $code, string $redisKeys): object
    {
        $redisKey = sprintf($redisKeys, $code);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $mapTideInfo = json_decode((string) json_encode($this->redisService->get($key)), false);
        }else{
            $myMapInfo = new MapTideData();
            $myMapInfo->setMapCode($code);
            $mapTideInfo = $this->mapRepository->getMapTideInfo($myMapInfo);
        }
        return $mapTideInfo;
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

    // 확률에 따른 물고기 등급 뽑기
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