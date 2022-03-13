<?php

namespace App\Domain\Repair\Service;

use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\Repair\Repository\RepairRepository;
use Psr\Log\LoggerInterface;

class RepairService extends BaseService
{
    protected RepairRepository $repairRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

    private const REPAIR_REDIS_KEY = 'repair:%s';

    public function __construct(LoggerInterface $logger
        ,RepairRepository $repairRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->repairRepository = $repairRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }
}