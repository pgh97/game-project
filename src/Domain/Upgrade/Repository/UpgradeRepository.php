<?php

namespace App\Domain\Upgrade\Repository;

use App\Domain\Upgrade\Entity\FishingItemUpgradeData;
use App\Domain\Upgrade\Entity\ShipItemUpgradeData;

interface UpgradeRepository
{
    /**
     * @param FishingItemUpgradeData $upgradeData
     * @return FishingItemUpgradeData
     */
    public function getFishingItemUpgradeData(FishingItemUpgradeData $upgradeData): FishingItemUpgradeData;

    /**
     * @param ShipItemUpgradeData $upgradeData
     * @return ShipItemUpgradeData
     */
    public function getShipItemUpgradeData(ShipItemUpgradeData $upgradeData): ShipItemUpgradeData;
}