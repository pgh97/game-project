<?php

namespace App\Domain\Repair\Repository;

use App\Domain\Repair\Entity\ItemRepairInfoData;

interface RepairRepository
{
    /**
     * @param ItemRepairInfoData $repairInfoData
     * @return ItemRepairInfoData
     */
    public function getItemRepairInfo(ItemRepairInfoData $repairInfoData):ItemRepairInfoData;
}