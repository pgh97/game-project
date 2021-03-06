<?php

namespace App\Application\Actions\Upgrade;

use App\Application\Actions\Action;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\RedisService;
use App\Domain\Common\Service\ScribeService;
use App\Domain\Fishing\Repository\FishingRepository;
use App\Domain\Upgrade\Repository\UpgradeRepository;
use App\Domain\User\Repository\UserRepository;
use Psr\Log\LoggerInterface;

abstract class UpgradeAction extends Action
{
    protected UpgradeRepository $upgradeRepository;
    protected UserRepository $userRepository;
    protected FishingRepository $fishingRepository;
    protected CommonRepository $commonRepository;
    protected ScribeService $scribeService;
    protected RedisService $redisService;

    public function __construct(LoggerInterface $logger
        ,UpgradeRepository $upgradeRepository
        ,UserRepository $userRepository
        ,FishingRepository $fishingRepository
        ,CommonRepository $commonRepository
        ,ScribeService $scribeService
        ,RedisService $redisService)
    {
        parent::__construct($logger);
        $this->upgradeRepository = $upgradeRepository;
        $this->userRepository = $userRepository;
        $this->fishingRepository = $fishingRepository;
        $this->commonRepository = $commonRepository;
        $this->scribeService = $scribeService;
        $this->redisService = $redisService;
    }
}