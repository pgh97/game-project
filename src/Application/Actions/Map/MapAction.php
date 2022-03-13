<?php

namespace App\Application\Actions\Map;

use App\Application\Actions\Action;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\RedisService;
use App\Domain\Map\Repository\MapRepository;
use Psr\Log\LoggerInterface;

abstract class MapAction extends Action
{
    protected MapRepository $mapRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;

    public function __construct(LoggerInterface $logger
    ,MapRepository $mapRepository
    ,CommonRepository $commonRepository
    ,RedisService $redisService)
    {
        parent::__construct($logger);
        $this->mapRepository = $mapRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }
}