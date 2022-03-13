<?php

namespace App\Application\Actions\Upgrade;

use App\Application\Actions\Action;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\RedisService;
use App\Domain\Upgrade\Repository\UpgradeRepository;
use Psr\Log\LoggerInterface;

abstract class UpgradeAction extends Action
{
    protected UpgradeRepository $upgradeRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;

    public function __construct(LoggerInterface $logger
        ,UpgradeRepository $upgradeRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        parent::__construct($logger);
        $this->upgradeRepository = $upgradeRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }
}