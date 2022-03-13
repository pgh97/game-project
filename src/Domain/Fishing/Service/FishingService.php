<?php

namespace App\Domain\Fishing\Service;

use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\Fishing\Repository\FishingRepository;
use Psr\Log\LoggerInterface;

class FishingService extends BaseService
{
    protected FishingRepository $fishingRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

    private const FISHING_REDIS_KEY = 'fishing:%s';

    public function __construct(LoggerInterface $logger
        ,FishingRepository $fishingRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->fishingRepository = $fishingRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }
}