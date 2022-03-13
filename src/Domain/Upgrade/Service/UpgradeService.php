<?php

namespace App\Domain\Upgrade\Service;

use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\Upgrade\Repository\UpgradeRepository;
use Psr\Log\LoggerInterface;

class UpgradeService extends BaseService
{
    protected UpgradeRepository $upgradeRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

    private const UPGRADE_REDIS_KEY = 'upgrade:%s';

    public function __construct(LoggerInterface $logger
        ,UpgradeRepository $upgradeRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->upgradeRepository = $upgradeRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }
}