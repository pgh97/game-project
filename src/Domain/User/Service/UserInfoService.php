<?php

namespace App\Domain\User\Service;

use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Repository\UserInfoRepository;
use Psr\Log\LoggerInterface;

class UserInfoService
{
    protected UserInfoRepository $infoRepository;
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger, UserInfoRepository $infoRepository)
    {
        $this->logger = $logger;
        $this->infoRepository = $infoRepository;
    }

    public function createUserInfo(array $input): int
    {
        return 0;
    }

    public function getUserInfo(array $input): UserInfo
    {
        return 0;
    }
}