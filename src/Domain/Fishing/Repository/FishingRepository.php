<?php

namespace App\Domain\Fishing\Repository;

use App\Domain\Common\Entity\SearchInfo;
use App\Domain\User\Entity\UserFishInventoryInfo;

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
}