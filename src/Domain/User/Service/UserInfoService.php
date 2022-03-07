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
        $data = json_decode((string) json_encode($input), false);
        $myUserInfo = new UserInfo();

        $myUserInfo->setAccountCode($data->decoded->data->accountCode);
        $myUserInfo->setUserNickNm($data->user_nicknm);
        $myUserInfo->setUserExperience($data->user_experience);
        $myUserInfo->setMoneyGold($data->money_gold);
        $myUserInfo->setMoneyPearl($data->money_pearl);
        $myUserInfo->setFatigue($data->fatigue);

        $userInfo = $this->infoRepository->createUserInfo($myUserInfo);
        $this->logger->info("create user service");
        return $userInfo;
    }

    public function getUserInfo(array $input): UserInfo
    {
        return 0;
    }
}