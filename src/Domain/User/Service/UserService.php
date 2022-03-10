<?php

namespace App\Domain\User\Service;

use App\Domain\Common\Entity\Level\UserLevelInfoData;
use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Repository\UserRepository;
use Psr\Log\LoggerInterface;

class UserService extends BaseService
{
    protected UserRepository $userRepository;
    protected LoggerInterface $logger;
    protected RedisService $redisService;

    private const REDIS_KEY = 'user:%s';

    public function __construct(LoggerInterface $logger
        , UserRepository                        $userRepository
        , RedisService                          $redisService)
    {
        $this->logger = $logger;
        $this->userRepository = $userRepository;
        $this->redisService = $redisService;
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
        $levelInfo = $this->userRepository->getUserLevelInfo($myLevelInfo);

        $myUserInfo->setLevelCode($levelInfo->getLevelCode());
        $myUserInfo->setFatigue($levelInfo->getMaxFatigue());
        $myUserInfo->setUseInventoryCount($levelInfo->getInventoryCount());

        $userCode = $this->userRepository->createUserInfo($myUserInfo);
        if (self::isRedisEnabled() === true) {
            $myUserInfo->setUserCode($userCode);
            $user = $this->userRepository->getUserInfo($myUserInfo);
            $this->saveInCache($userCode, $user);
        }
        $this->logger->info("create user service");
        return $userCode;
    }

    public function getUserInfo(array $input): object
    {
        $data = json_decode((string) json_encode($input), false);
        $myUserInfo = new UserInfo();
        $myUserInfo->setAccountCode($data->decoded->data->accountCode);
        $myUserInfo->setUserCode($data->userCode);

        $userInfo = $this->userRepository->getUserInfo($myUserInfo);
        $this->logger->info("get user service");
        return $userInfo->toJson();
    }

    public function getUserInfoList(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $search = new SearchInfo();
        $search->setAccountCode($data->decoded->data->accountCode);
        $search->setLimit($data->limit);
        $search->setOffset($data->offset);

        $userInfoArray = $this->userRepository->getUserInfoList($search);
        $userInfoArrayCnt = $this->userRepository->getUserInfoListCnt($search);

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

        $this->userRepository->ModifyUserInfo($myUserInfo);
        $result = $this->userRepository->getUserInfo($myUserInfo);
        $this->logger->info("update user service");
        return $result;
    }

    protected function saveInCache(int $userCode, object $user): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $userCode);
        $key = $this->redisService->generateKey($redisKey);
        //$this->redisService->setex($key, $user);
        $this->redisService->set($key, $user);
    }

    protected function deleteFromCache(int $userCode): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $userCode);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->del([$key]);
    }
}