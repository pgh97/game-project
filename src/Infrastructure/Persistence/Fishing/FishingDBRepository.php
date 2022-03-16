<?php

namespace App\Infrastructure\Persistence\Fishing;

use App\Domain\Common\Entity\SearchInfo;
use App\Domain\User\Entity\UserFishInventoryInfo;
use App\Domain\Fishing\Repository\FishingRepository;
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
}