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
     * @param FishingItemUpgradeData $upgradeData
     * @return int
     */
    public function getFishingItemUpgradeCode(FishingItemUpgradeData $upgradeData): int;

    /**
     * @param ShipItemUpgradeData $upgradeData
     * @return ShipItemUpgradeData
     */
    public function getShipItemUpgradeData(ShipItemUpgradeData $upgradeData): ShipItemUpgradeData;
}