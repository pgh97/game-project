<?php

namespace App\Domain\Quest\Service;

use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\Quest\Repository\QuestRepository;
use Psr\Log\LoggerInterface;

class QuestService extends BaseService
{
    protected QuestRepository $questRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

    private const QUEST_REDIS_KEY = 'quest:%s';

    public function __construct(LoggerInterface $logger
        ,QuestRepository $questRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->questRepository = $questRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }
}