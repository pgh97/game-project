<?php

namespace App\Application\Actions\Quest;

use App\Application\Actions\Action;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\RedisService;
use App\Domain\Quest\Repository\QuestRepository;
use Psr\Log\LoggerInterface;

abstract class QuestAction extends Action
{
    protected QuestRepository $questRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;
    public function __construct(LoggerInterface $logger
        ,QuestRepository $questRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        parent::__construct($logger);
        $this->questRepository = $questRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }
}