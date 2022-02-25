<?php

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Repository\UserInfoRepository;

class UserInfoDBRepository implements UserInfoRepository
{

    public function createUserInfo(UserInfo $userInfo): int
    {
        return 0;
    }

    public function getUserInfo(UserInfo $userInfo): UserInfo
    {
        return 0;
    }
}