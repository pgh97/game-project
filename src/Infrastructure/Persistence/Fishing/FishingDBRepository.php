<?php

namespace App\Infrastructure\Persistence\Fishing;

use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Fishing\Entity\FishingLineGradeData;
use App\Domain\Fishing\Entity\FishingReelGradeData;
use App\Domain\Fishing\Entity\FishingRodGradeData;
use App\Domain\User\Entity\UserFishInventoryInfo;
use App\Domain\Fishing\Repository\FishingRepository;
use App\Domain\User\Entity\UserInventoryInfo;
use App\Infrastructure\Persistence\BaseRepository;

class FishingDBRepository extends BaseRepository implements FishingRepository
{

    public function getUserFishInventory(UserFishInventoryInfo $inventoryInfo): UserFishInventoryInfo
    {
        $query = '
           SELECT 
                fish_inventory_code      AS fishInventoryCode
                ,user_code               AS userCode
                ,map_code                AS mapCode
                ,fish_grade_code         AS fishGradeCode
                ,create_date             AS createDate
            FROM `user_fish_inventory_info`
            WHERE user_code = :userCode AND fish_inventory_code = :fishInventoryCode
        ';

        $statement = $this->database->prepare($query);
        $userCode = $inventoryInfo->getUserCode();
        $fishInventoryCode = $inventoryInfo->getFishInventoryCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':fishInventoryCode', $fishInventoryCode);
        $statement->execute();
        return $statement->fetchObject(UserFishInventoryInfo::class);
    }

    public function getUserFishInventoryList(SearchInfo $searchInfo): array
    {
        $query = '
            SELECT 
                fish_inventory_code      AS fishInventoryCode
                ,user_code               AS userCode
                ,map_code                AS mapCode
                ,fish_grade_code         AS fishGradeCode
                ,create_date             AS createDate
            FROM `user_fish_inventory_info`
            WHERE user_code = :userCode AND map_code = :mapCode
            ORDER BY fish_inventory_code
            LIMIT :offset , :limit
        ';

        $statement = $this->database->prepare($query);
        $userCode = $searchInfo->getUserCode();
        $mapCode = $searchInfo->getItemCode();
        $offset = $searchInfo->getOffset();
        $limit = $searchInfo->getLimit();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':mapCode', $mapCode);
        $statement->bindParam(':offset', $offset);
        $statement->bindParam(':limit', $limit);
        $statement->execute();
        return (array) $statement->fetchAll();
    }

    public function getUserFishInventoryListCnt(SearchInfo $searchInfo): int
    {
        $query = '
            SELECT 
                COUNT(*)    AS count
            FROM `user_fish_inventory_info`
            WHERE user_code = :userCode AND map_code = :mapCode
        ';

        $statement = $this->database->prepare($query);
        $userCode = $searchInfo->getUserCode();
        $mapCode = $searchInfo->getItemCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':mapCode', $mapCode);
        $statement->execute();
        return $statement->fetchColumn();
    }

    public function createUserFishInventory(UserFishInventoryInfo $userFishInventoryInfo): int
    {
        $query = '
            INSERT INTO `user_fish_inventory_info`
                (`user_code`, `map_code`, `fish_grade_code`,`create_date`)
            VALUES
                (:userCode, :mapCode, :fishGradeCode, NOW())
        ';


        $statement = $this->database->prepare($query);

        $userCode = $userFishInventoryInfo->getUserCode();
        $mapCode = $userFishInventoryInfo->getMapCode();
        $fishGradeCode = $userFishInventoryInfo->getFishGradeCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':mapCode', $mapCode);
        $statement->bindParam(':fishGradeCode', $fishGradeCode);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    public function deleteUserFishInventory(UserFishInventoryInfo $userFishInventoryInfo): int
    {
        $query = '
            DELETE FROM `user_fish_inventory_info`       
            WHERE user_code =:userCode 
        ';

        if(!empty($userFishInventoryInfo->getFishInventoryCode())){
            $query .= 'AND fish_inventory_code = :fishInventoryCode';
        }

        $statement = $this->database->prepare($query);
        $userCode= $userFishInventoryInfo->getUserCode();
        $statement->bindParam(':userCode', $userCode);

        if(!empty($userFishInventoryInfo->getFishInventoryCode())){
            $fishInventoryCode= $userFishInventoryInfo->getFishInventoryCode();
            $statement->bindParam(':fishInventoryCode', $fishInventoryCode);
        }

        $statement->execute();
        return $statement->rowCount();
    }

    public function getFishingRodGradeData(UserInventoryInfo $inventoryInfo): FishingRodGradeData
    {
        $query = '
            SELECT 
                item_grade_code              AS itemGradeCode
                ,item_code                   AS itemCode
                ,item_type                   AS itemType
                ,grade_code                  AS gradeCode
                ,durability                  AS durability
                ,suppress_probability        AS suppressProbability
                ,hooking_probability         AS hookingProbability
                ,IF(max_weight IS NULL, 0, max_weight)                  AS maxWeight
                ,IF(min_weight IS NULL, 0, min_weight)                  AS minWeight
                ,max_upgrade                 AS maxUpgrade
                ,create_date                 AS createDate
            FROM `fishing_rod_grade_data`
            WHERE item_grade_code = :itemCode
        ';

        $statement = $this->database->prepare($query);
        $itemCode = $inventoryInfo->getItemCode();

        $statement->bindParam(':itemCode', $itemCode);
        $statement->execute();
        return $statement->fetchObject(FishingRodGradeData::class);
    }

    public function getFishingLineGradeData(UserInventoryInfo $inventoryInfo): FishingLineGradeData
    {
        $query = '
            SELECT 
                item_grade_code              AS itemGradeCode
                ,item_code                   AS itemCode
                ,grade_code                  AS gradeCode
                ,durability                  AS durability
                ,suppress_probability        AS suppressProbability
                ,hooking_probability         AS hookingProbability
                ,IF(max_weight IS NULL, 0, max_weight)                  AS maxWeight
                ,IF(min_weight IS NULL, 0, min_weight)                  AS minWeight
                ,max_upgrade                 AS maxUpgrade
                ,create_date                 AS createDate
            FROM `fishing_line_grade_data`
            WHERE item_grade_code = :itemCode
        ';

        $statement = $this->database->prepare($query);
        $itemCode = $inventoryInfo->getItemCode();

        $statement->bindParam(':itemCode', $itemCode);
        $statement->execute();
        return $statement->fetchObject(FishingLineGradeData::class);
    }

    public function getFishingReelGradeData(UserInventoryInfo $inventoryInfo): FishingReelGradeData
    {
        $query = '
            SELECT 
                item_grade_code              AS itemGradeCode
                ,item_code                   AS itemCode
                ,grade_code                  AS gradeCode
                ,durability                  AS durability
                ,IF(reel_number IS NULL, 0, reel_number)                 AS reelNumber
                ,IF(reel_winding_amount IS NULL, 0, reel_winding_amount)         AS reelWindingAmount
                ,max_upgrade                 AS maxUpgrade
                ,create_date                 AS createDate
            FROM `fishing_reel_grade_data`
            WHERE item_grade_code = :itemCode
        ';

        $statement = $this->database->prepare($query);
        $itemCode = $inventoryInfo->getItemCode();

        $statement->bindParam(':itemCode', $itemCode);
        $statement->execute();
        return $statement->fetchObject(FishingReelGradeData::class);
    }
}