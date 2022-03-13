<?php

namespace App\Infrastructure\Persistence\Map;

use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Map\Entity\MapInfoData;
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
            ORDER BY map_code
            LIMIT :offset , :limit
        ';

        $statement = $this->database->prepare($query);
        $offset = $searchInfo->getOffset();
        $limit = $searchInfo->getLimit();

        $statement->bindParam(':offset', $offset);
        $statement->bindParam(':limit', $limit);
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
}