<?php

namespace App\Domain\Upgrade\Service;

use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\Upgrade\Repository\UpgradeRepository;
use App\Domain\User\Repository\UserRepository;
use Psr\Log\LoggerInterface;

class UpgradeService extends BaseService
{
    protected UpgradeRepository $upgradeRepository;
    protected UserRepository $userRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

    private const UPGRADE_REDIS_KEY = 'upgrade:%s';

    public function __construct(LoggerInterface $logger
        ,UpgradeRepository $upgradeRepository
        ,UserRepository $userRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->upgradeRepository = $upgradeRepository;
        $this->userRepository = $userRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }

    public function modifyUpgradeFishingItem(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        return [
            'message' => "테스트",
        ];
    }

    public function modifyUpgradeShipItem(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        return [
            'message' => "테스트",
        ];
    }
}