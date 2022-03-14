<?php

namespace App\Infrastructure\Persistence\Map;

use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Map\Entity\MapInfoData;
use App\Domain\Map\Entity\MapTideData;
use App\Domain\Map\Repository\MapRepository;
use App\Infrastructure\Persistence\BaseRepository;

class MapDBRepository extends BaseRepository implements MapRepository
{

    public function getMapInfo(MapInfoData $mapInfoData): MapInfoData
    {
        $query = '
            SELECT 
                map_code                AS mapCode
                ,map_name               AS mapName
                ,max_depth              AS maxDepth
                ,min_level              AS minLevel
                ,distance               AS distance
                ,money_code             AS moneyCode
                ,departure_price        AS departurePrice
                ,departure_time         AS departureTime
                ,per_durability         AS perDurability
                ,map_fish_count         AS mapFishCount
                ,fish_size_probability  AS fishSizeProbability
                ,create_date            AS createDate
            FROM `map_info_data`
            WHERE map_code = :mapCode
        ';

        $statement = $this->database->prepare($query);
        $mapCode = $mapInfoData->getMapCode();

        $statement->bindParam(':mapCode', $mapCode);
        $statement->execute();

        return $statement->fetchObject(MapInfoData::class);
    }

    public function getMapInfoList(SearchInfo $searchInfo): array
    {
        $queryFront = '
            SELECT 
                map_code                AS mapCode
                ,map_name               AS mapName
                ,max_depth              AS maxDepth
                ,min_level              AS minLevel
                ,distance               AS distance
                ,money_code             AS moneyCode
                ,departure_price        AS departurePrice
                ,departure_time         AS departureTime
                ,per_durability         AS perDurability
                ,map_fish_count         AS mapFishCount
                ,fish_size_probability  AS fishSizeProbability
                ,create_date            AS createDate
            FROM `map_info_data`
            ORDER BY map_code ';
        if(!empty($searchInfo->getLimit())){
            $queryEnd = 'LIMIT :offset , :limit ';
        }else{
            $queryEnd = '';
        }

        $statement = $this->database->prepare($queryFront.$queryEnd);

        if(!empty($searchInfo->getLimit())){
            $offset = $searchInfo->getOffset();
            $limit = $searchInfo->getLimit();
            $statement->bindParam(':offset', $offset);
            $statement->bindParam(':limit', $limit);
        }
        $statement->execute();

        return (array) $statement->fetchAll();
    }

    public function getMapInfoListCnt(SearchInfo $searchInfo): int
    {
        $query = '
            SELECT 
                COUNT(*)    AS count
            FROM `map_info_data`
        ';

        $statement = $this->database->prepare($query);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public function getMapTideList(SearchInfo $searchInfo): array
    {
        $queryFront = '
            SELECT 
                MT.map_tide_code           AS mapTideCode
                ,MT.map_code               AS mapCode
                ,MT.tide_code              AS tideCode
                ,MT.tide_sort              AS tideSort
                ,T.high_tide_time1         AS highTideTime1
                ,T.low_tide_time1          AS lowTideTime1
                ,T.high_tide_time2         AS highTideTime2
                ,T.low_tide_time2          AS lowTideTime2
                ,T.water_splash_time       AS waterSplashTime
                ,T.appear_probability      AS appearProbability
                ,MT.create_date            AS createDate
            FROM `map_tide_data` MT
            JOIN `tide_info_data` T ON MT.tide_code = T.tide_code
            WHERE MT.tide_sort = :tideSort
            ORDER BY MT.map_code 
        ';

        if(!empty($searchInfo->getLimit())){
            $queryEnd = 'LIMIT :offset , :limit ';
        }else{
            $queryEnd = '';
        }

        $statement = $this->database->prepare($queryFront.$queryEnd);
        $tideSort = $searchInfo->getSort();

        $statement->bindParam(':tideSort', $tideSort);

        if(!empty($searchInfo->getLimit())){
            $offset = $searchInfo->getOffset();
            $limit = $searchInfo->getLimit();
            $statement->bindParam(':offset', $offset);
            $statement->bindParam(':limit', $limit);
        }

        $statement->execute();
        return (array) $statement->fetchAll();
    }

    public function getMapTideInfo(MapTideData $mapTideData): MapTideData
    {
        $query = '
            SELECT 
                MT.map_tide_code           AS mapTideCode
                ,MT.map_code               AS mapCode
                ,MT.tide_code              AS tideCode
                ,MT.tide_sort              AS tideSort
                ,T.high_tide_time1         AS highTideTime1
                ,T.low_tide_time1          AS lowTideTime1
                ,T.high_tide_time2         AS highTideTime2
                ,T.low_tide_time2          AS lowTideTime2
                ,T.water_splash_time       AS waterSplashTime
                ,T.appear_probability      AS appearProbability
                ,MT.create_date            AS createDate
            FROM `map_tide_data` MT
            JOIN `tide_info_data` T ON MT.tide_code = T.tide_code
            WHERE MT.map_code = :mapCode 
        ';

        $statement = $this->database->prepare($query);
        $mapCode = $mapTideData->getMapCode();

        $statement->bindParam(':mapCode', $mapCode);
        $statement->execute();
        return $statement->fetchObject(MapTideData::class);
    }
}