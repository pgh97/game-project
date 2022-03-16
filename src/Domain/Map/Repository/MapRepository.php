<?php

namespace App\Domain\Map\Repository;

use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Map\Entity\MapInfoData;
use App\Domain\Map\Entity\MapTideData;

interface MapRepository
{
    /**
     * @param MapInfoData $mapInfoData
     * @return MapInfoData
     */
    public function getMapInfo(MapInfoData $mapInfoData): MapInfoData;

    /**
     * @param SearchInfo $searchInfo
     * @return array
     */
    public function getMapInfoList(SearchInfo $searchInfo): array;

    /**
     * @param SearchInfo $searchInfo
     * @return int
     */
    public function getMapInfoListCnt(SearchInfo $searchInfo): int;

    /**
     * @param MapTideData $mapTideData
     * @return MapTideData
     */
    public function getMapTideInfo(MapTideData $mapTideData): MapTideData;

    /**
     * @param SearchInfo $searchInfo
     * @return array
     */
    public function getMapTideList(SearchInfo $searchInfo): array;

    /**
     * @param SearchInfo $searchInfo
     * @return array
     */
    public function getMapFishList(SearchInfo $searchInfo): array;
}