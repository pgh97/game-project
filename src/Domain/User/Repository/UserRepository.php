<?php

namespace App\Domain\User\Repository;

use App\Domain\Common\Entity\Level\UserLevelInfoData;
use App\Domain\Common\Entity\SearchInfo;
use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Entity\UserInventoryInfo;
use App\Domain\User\Entity\UserShipInfo;
use App\Domain\User\Entity\UserWeatherHistory;

interface UserRepository
{
    /**
     * @param UserInfo $userInfo
     * @return int
     */
    public function createUserInfo(UserInfo $userInfo): int;

    /**
     * @param UserWeatherHistory $userWeatherHistory
     * @return int
     */
    public function createUserWeather(UserWeatherHistory $userWeatherHistory): int;

    /**
     * @param UserInventoryInfo $inventoryInfo
     * @return int
     */
    public function createUserInventoryInfo(UserInventoryInfo $inventoryInfo): int;

    /**
     * @param UserShipInfo $shipInfo
     * @return int
     */
    public function createUserShipInfo(UserShipInfo $shipInfo): int;

    /**
     * @param UserInfo $userInfo
     * @return UserInfo
     */
    public function getUserInfo(UserInfo $userInfo): UserInfo;

    /**
     * @param SearchInfo $searchInfo
     * @return array
     */
    public function getUserInfoList(SearchInfo $searchInfo): array;

    /**
     * @param SearchInfo $searchInfo
     * @return int
     */
    public function getUserInfoListCnt(SearchInfo $searchInfo): int;

    /**
     * @param UserLevelInfoData $levelInfo
     * @return UserLevelInfoData
     */
    public function getUserLevelInfo(UserLevelInfoData $levelInfo): UserLevelInfoData;

    /**
     * @param UserWeatherHistory $userWeatherHistory
     * @return UserWeatherHistory
     */
    public function getUserWeatherHistory(UserWeatherHistory $userWeatherHistory): UserWeatherHistory;

    /**
     * @param UserShipInfo $shipInfo
     * @return UserShipInfo
     */
    public function getUserShipInfo(UserShipInfo $shipInfo): UserShipInfo;

    /**
     * @param SearchInfo $searchInfo
     * @return array
     */
    public function getUserInventoryList(SearchInfo $searchInfo): array;

    /**
     * @param SearchInfo $searchInfo
     * @return int
     */
    public function getUserInventoryListCnt(SearchInfo $searchInfo): int;

    /**
     * @param UserInventoryInfo $inventoryInfo
     * @return UserInventoryInfo
     */
    public function getUserInventory(UserInventoryInfo $inventoryInfo): UserInventoryInfo;

    /**
     * @param UserInfo $userInfo
     * @return int
     */
    public function modifyUserInfo(UserInfo $userInfo): int;
    /**
     * @param UserWeatherHistory $userWeatherHistory
     * @return int
     */
    public function modifyUserWeatherHistory(UserWeatherHistory $userWeatherHistory): int;
}