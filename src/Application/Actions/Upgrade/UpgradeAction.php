<?php

namespace App\Application\Actions\Upgrade;

use App\Application\Actions\Action;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\RedisService;
use App\Domain\Upgrade\Repository\UpgradeRepository;
use App\Domain\User\Repository\UserRepository;
use Psr\Log\LoggerInterface;

abstract class UpgradeAction extends Action
{
    protected UpgradeRepository $upgradeRepository;
    protected UserRepository $userRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;

    public function __construct(LoggerInterface $logger
        ,UpgradeRepository $upgradeRepository
        ,UserRepository $userRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        parent::__construct($logger);
        $this->upgradeRepository = $upgradeRepository;
        $this->userRepository = $userRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }
}