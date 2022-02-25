<?php

namespace App\Domain\User\Repository;

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
}