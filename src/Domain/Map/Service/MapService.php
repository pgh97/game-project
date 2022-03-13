<?php

namespace App\Domain\Map\Service;

use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\Map\Entity\MapInfoData;
use App\Domain\Map\Repository\MapRepository;
use Psr\Log\LoggerInterface;

class MapService extends BaseService
{
    protected MapRepository $mapRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

    private const MAP_REDIS_KEY = 'map:%s';

    public function __construct(LoggerInterface $logger
    ,MapRepository $mapRepository
    ,CommonRepository $commonRepository
    ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->mapRepository = $mapRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }

    public function getMapInfo(array $input): MapInfoData
    {
        $data = json_decode((string) json_encode($input), false);
        $myMapInfo = new MapInfoData();
        $myMapInfo->setMapCode($data->mapCode);

        $mapInfo = $this->mapRepository->getMapInfo($myMapInfo);
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
}