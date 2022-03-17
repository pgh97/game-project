<?php

namespace App\Domain\User\Repository;

use App\Domain\Common\Entity\Level\UserLevelInfoData;
use App\Domain\Common\Entity\SearchInfo;
use App\Domain\User\Entity\UserChoiceItemInfo;
use App\Domain\User\Entity\UserFishInventoryInfo;
use App\Domain\User\Entity\UserGitfBoxInfo;
use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Entity\UserInventoryInfo;
use App\Domain\User\Entity\UserQuestInfo;
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
     * @param UserChoiceItemInfo $choiceItemInfo
     * @return int
     */
    public function createUserFishingItem(UserChoiceItemInfo $choiceItemInfo): int;

    /**
     * @param UserShipInfo $shipInfo
     * @return int
     */
    public function createUserShipInfo(UserShipInfo $shipInfo): int;

    /**
     * @param UserGitfBoxInfo $boxInfo
     * @return int
     */
    public function createUserGiftBox(UserGitfBoxInfo $boxInfo): int;

    /**
     * @param UserFishInventoryInfo $userFishInventoryInfo
     * @return int
     */
    public function createUserInventoryFish(UserFishInventoryInfo $userFishInventoryInfo): int;

    /**
     * @param UserFishInventoryInfo $userFishInventoryInfo
     * @return int
     */
    public function createUserFishDictionary(UserFishInventoryInfo $userFishInventoryInfo): int;

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
     * @param SearchInfo $searchInfo
     * @return array
     */
    public function getUserFishingItemList(SearchInfo $searchInfo): array;

    /**
     * @param SearchInfo $searchInfo
     * @return int
     */
    public function getUserFishingItemListCnt(SearchInfo $searchInfo): int;

    /**
     * @param UserChoiceItemInfo $choiceItemInfo
     * @return UserChoiceItemInfo
     */
    public function getUserFishingItem(UserChoiceItemInfo $choiceItemInfo): UserChoiceItemInfo;

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

    /**
     * @param UserShipInfo $userShipInfo
     * @return int
     */
    public function modifyUserShip(UserShipInfo $userShipInfo): int;

    /**
     * @param UserInfo $userInfo
     * @return int
     */
    public function deleteUserInfo(UserInfo $userInfo): int;

    /**
     * @param UserInventoryInfo $inventoryInfo
     * @return int
     */
    public function deleteUserInventory(UserInventoryInfo $inventoryInfo): int;

    /**
     * @param SearchInfo $searchInfo
     * @return int
     */
    public function deleteUserInventoryFish(SearchInfo $searchInfo): int;

    /**
     * @param UserInfo $userInfo
     * @return int
     */
    public function modifyUserLevel(UserInfo $userInfo): int;

    /**
     * @param SearchInfo $searchInfo
     * @return int
     */
    public function getUserFishDictionaryCnt(SearchInfo $searchInfo): int;

    /**
     * @param SearchInfo $searchInfo
     * @return array
     */
    public function getUserGiftBoxList(SearchInfo $searchInfo): array;

    /**
     * @param SearchInfo $searchInfo
     * @return int
     */
    public function getUserGiftBoxListCnt(SearchInfo $searchInfo): int;

    /**
     * @param UserGitfBoxInfo $boxInfo
     * @return UserGitfBoxInfo
     */
    public function getUserGiftBoxInfo(UserGitfBoxInfo $boxInfo): UserGitfBoxInfo;

    /**
     * @param UserInventoryInfo $inventoryInfo
     * @return int
     */
    public function modifyUserInventoryFishDurability(UserInventoryInfo $inventoryInfo): int;

    /**
     * @param UserInventoryInfo $inventoryInfo
     * @return int
     */
    public function createUserQuestInfo(UserQuestInfo $userQuestInfo): int;

    /**
     * @param UserQuestInfo $userQuestInfo
     * @return int
     */
    public function getUserQuestInfoCnt(UserQuestInfo $userQuestInfo): int;

    /**
     * @param UserGitfBoxInfo $boxInfo
     * @return int
     */
    public function createUserGiftBoxToInventory(UserGitfBoxInfo $boxInfo): int;

    /**
     * @param UserGitfBoxInfo $boxInfo
     * @return int
     */
    public function modifyUserGiftBoxStatus(UserGitfBoxInfo $boxInfo): int;

    /**
     * @param UserGitfBoxInfo $boxInfo
     * @return int
     */
    public function modifyUserInfoGiftBox(UserGitfBoxInfo $boxInfo): int;
}