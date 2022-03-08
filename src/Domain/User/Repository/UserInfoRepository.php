<?php

namespace App\Domain\User\Repository;

use App\Domain\Common\Entity\UserLevelInfoData;
use App\Domain\Common\SearchInfo;
use App\Domain\User\Entity\UserInfo;

interface UserInfoRepository
{
    /**
     * @param UserInfo $userInfo
     * @return int
     */
    public function createUserInfo(UserInfo $userInfo): int;

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
     * @param UserInfo $userInfo
     * @return int
     */
    public function modifyUserInfo(UserInfo $userInfo): int;
}