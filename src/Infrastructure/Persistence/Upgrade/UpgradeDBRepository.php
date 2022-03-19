<?php

namespace App\Infrastructure\Persistence\Upgrade;

use App\Domain\Upgrade\Entity\FishingItemUpgradeData;
use App\Domain\Upgrade\Entity\ShipItemUpgradeData;
use App\Domain\Upgrade\Repository\UpgradeRepository;
use App\Infrastructure\Persistence\BaseRepository;

class UpgradeDBRepository extends BaseRepository implements UpgradeRepository
{

    public function getFishingItemUpgradeData(FishingItemUpgradeData $upgradeData): FishingItemUpgradeData
    {
        $query = '
            SELECT 
                upgrade_code             AS upgradeCode
                ,item_grade_code         AS itemGradeCode
                ,item_type               AS itemType
                ,upgrade_level           AS upgradeLevel
                ,upgrade_item_code       AS upgradeItemCode
                ,upgrade_item_count      AS upgradeItemCount
                ,money_code              AS moneyCode
                ,upgrade_price           AS upgradePrice
                ,add_probability         AS addProbability
                ,create_date             AS createDate
            FROM `fishing_item_upgrade_data`
            WHERE upgrade_code = :upgradeCode
        ';

        $statement = $this->database->prepare($query);
        $upgradeCode = $upgradeData->getUpgradeCode();

        $statement->bindParam(':upgradeCode', $upgradeCode);
        $statement->execute();
        return $statement->fetchObject(FishingItemUpgradeData::class);
    }

    public function getShipItemUpgradeData(ShipItemUpgradeData $upgradeData): ShipItemUpgradeData
    {
        $query = '
            SELECT 
                upgrade_code             AS upgradeCode
                ,ship_code               AS shipCode
                ,upgrade_level           AS upgradeLevel
                ,money_code              AS moneyCode
                ,upgrade_price           AS upgradePrice
                ,add_fuel                AS addFuel
                ,add_probability         AS addProbability
                ,upgrade_probability     AS upgradeProbability
                ,create_date             AS createDate
            FROM `ship_item_upgrade_data`
            WHERE upgrade_code = :upgradeCode
        ';

        $statement = $this->database->prepare($query);
        $upgradeCode = $upgradeData->getUpgradeCode();

        $statement->bindParam(':upgradeCode', $upgradeCode);
        $statement->execute();
        return $statement->fetchObject(ShipItemUpgradeData::class);
    }

    public function getFishingItemUpgradeCode(FishingItemUpgradeData $upgradeData): int
    {
        $query = '
            SELECT 
                upgrade_code             AS upgradeCode
            FROM `fishing_item_upgrade_data`
            WHERE item_grade_code=:itemGradeCode AND item_type=:itemType AND upgrade_level=:upgradeLevel
        ';

        $statement = $this->database->prepare($query);
        $itemGradeCode = $upgradeData->getItemGradeCode();
        $itemType = $upgradeData->getItemType();
        $upgradeLevel = $upgradeData->getUpgradeLevel();

        $statement->bindParam(':itemGradeCode', $itemGradeCode);
        $statement->bindParam(':itemType', $itemType);
        $statement->bindParam(':upgradeLevel', $upgradeLevel);
        $statement->execute();
        return $statement->fetchColumn();
    }
}