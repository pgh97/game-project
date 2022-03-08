<?php

namespace App\Domain\User\Service;

use App\Domain\Common\Entity\UserLevelInfoData;
use App\Domain\Common\SearchInfo;
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
        $myUserInfo->setUserExperience(0);
        $myUserInfo->setMoneyGold(100);
        $myUserInfo->setMoneyPearl(10);
        $myUserInfo->setUseSaveItemCount(5);

        $myLevelInfo = new UserLevelInfoData();
        $myLevelInfo->setLevelCode(1);
        $levelInfo = $this->infoRepository->getUserLevelInfo($myLevelInfo);

        $myUserInfo->setLevelCode($levelInfo->getLevelCode());
        $myUserInfo->setFatigue($levelInfo->getMaxFatigue());
        $myUserInfo->setUseInventoryCount($levelInfo->getInventoryCount());

        $userInfo = $this->infoRepository->createUserInfo($myUserInfo);
        $this->logger->info("create user service");
        return $userInfo;
    }

    public function getUserInfo(array $input): object
    {
        $data = json_decode((string) json_encode($input), false);
        $myUserInfo = new UserInfo();
        $myUserInfo->setAccountCode($data->decoded->data->accountCode);
        $myUserInfo->setUserCode($data->userCode);

        $userInfo = $this->infoRepository->getUserInfo($myUserInfo);
        $this->logger->info("get user service");
        return $userInfo->toJson();
    }

    public function getUserInfoList(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $search = new SearchInfo();
        $search->setAccountCode($data->decoded->data->accountCode);
        $search->setLimit(10);
        $search->setOffset(0);

        $userInfoArray = $this->infoRepository->getUserInfoList($search);
        $userInfoArrayCnt = $this->infoRepository->getUserInfoListCnt($search);

        $this->logger->info("get list user service");
        if(array_filter($userInfoArray)){
            return [
                'userInfoList' => $userInfoArray,
                'totalCount' => $userInfoArrayCnt,
            ];
        }else{
            return [];
        }
    }

    public function modifyUserInfo(array $input): UserInfo
    {
        $data = json_decode((string) json_encode($input), false);
        $myUserInfo = new UserInfo();
        $myUserInfo->setAccountCode($data->decoded->data->accountCode);
        $myUserInfo->setUserCode($data->userCode);
        $myUserInfo->setUserNickNm($data->userNickNm);
        $myUserInfo->setLevelCode($data->levelCode);
        $myUserInfo->setUserExperience($data->userExperience);
        $myUserInfo->setMoneyGold($data->moneyGold);
        $myUserInfo->setMoneyPearl($data->moneyPearl);
        $myUserInfo->setFatigue($data->fatigue);

        $this->infoRepository->ModifyUserInfo($myUserInfo);
        $result = $this->infoRepository->getUserInfo($myUserInfo);
        $this->logger->info("update user service");
        return $result;
    }
}