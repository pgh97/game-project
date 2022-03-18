<?php

namespace App\Domain\Fishing\Repository;

use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Fishing\Entity\FishingLineGradeData;
use App\Domain\Fishing\Entity\FishingReelGradeData;
use App\Domain\Fishing\Entity\FishingRodGradeData;
use App\Domain\User\Entity\UserFishInventoryInfo;
use App\Domain\User\Entity\UserInventoryInfo;

interface FishingRepository
{
    /**
     * @param UserFishInventoryInfo $inventoryInfo
     * @return UserFishInventoryInfo
     */
    public function getUserFishInventory(UserFishInventoryInfo $inventoryInfo): UserFishInventoryInfo;

    /**
     * @param SearchInfo $searchInfo
     * @return array
     */
    public function getUserFishInventoryList(SearchInfo $searchInfo): array;

    /**
     * @param SearchInfo $searchInfo
     * @return int
     */
    public function getUserFishInventoryListCnt(SearchInfo $searchInfo): int;

    /**
     * @param UserFishInventoryInfo $userFishInventoryInfo
     * @return int
     */
    public function createUserFishInventory(UserFishInventoryInfo $userFishInventoryInfo): int;

    /**
     * @param UserFishInventoryInfo $userFishInventoryInfo
     * @return int
     */
    public function deleteUserFishInventory(UserFishInventoryInfo $userFishInventoryInfo): int;

    /**
     * @param UserInventoryInfo $inventoryInfo
     * @return FishingRodGradeData
     */
    public function getFishingRodGradeData(UserInventoryInfo $inventoryInfo): FishingRodGradeData;

    /**
     * @param UserInventoryInfo $inventoryInfo
     * @return FishingLineGradeData
     */
    public function getFishingLineGradeData(UserInventoryInfo $inventoryInfo): FishingLineGradeData;

    /**
     * @param UserInventoryInfo $inventoryInfo
     * @return FishingReelGradeData
     */
    public function getFishingReelGradeData(UserInventoryInfo $inventoryInfo): FishingReelGradeData;
}