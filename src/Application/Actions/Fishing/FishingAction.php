<?php

namespace App\Application\Actions\Fishing;

use App\Application\Actions\Action;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\RedisService;
use App\Domain\Fishing\Repository\FishingRepository;
use Psr\Log\LoggerInterface;

abstract class FishingAction extends Action
{
    protected FishingRepository $fishingRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;
    public function __construct(LoggerInterface $logger
        ,FishingRepository $fishingRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        parent::__construct($logger);
        $this->fishingRepository = $fishingRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }
}