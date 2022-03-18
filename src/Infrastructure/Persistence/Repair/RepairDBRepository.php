<?php

namespace App\Infrastructure\Persistence\Repair;

use App\Domain\Repair\Entity\ItemRepairInfoData;
use App\Domain\Repair\Repository\RepairRepository;
use App\Infrastructure\Persistence\BaseRepository;

class RepairDBRepository extends BaseRepository implements RepairRepository
{

    public function getItemRepairInfo(ItemRepairInfoData $repairInfoData): ItemRepairInfoData
    {
        $query = '
            SELECT 
                repair_code              AS repairCode
                ,item_code               AS itemCode
                ,item_type               AS itemType
                ,money_code              AS moneyCode
                ,repair_price            AS repairPrice
                ,create_date            AS createDate
            FROM `item_repair_info_data`
            WHERE item_code = :itemCode AND item_type = :itemType
        ';

        $statement = $this->database->prepare($query);
        $itemCode = $repairInfoData->getItemCode();
        $itemType = $repairInfoData->getItemType();

        $statement->bindParam(':itemCode', $itemCode);
        $statement->bindParam(':itemType', $itemType);
        $statement->execute();
        return $statement->fetchObject(ItemRepairInfoData::class);
    }
}