<?php

namespace App\Application\Actions\Repair;

use App\Application\Actions\Action;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\RedisService;
use App\Domain\Common\Service\ScribeService;
use App\Domain\Fishing\Repository\FishingRepository;
use App\Domain\Repair\Repository\RepairRepository;
use App\Domain\Upgrade\Repository\UpgradeRepository;
use App\Domain\User\Repository\UserRepository;
use Psr\Log\LoggerInterface;

abstract class RepairAction extends Action
{
    protected RepairRepository $repairRepository;
    protected UserRepository $userRepository;
    protected FishingRepository $fishingRepository;
    protected UpgradeRepository $upgradeRepository;
    protected CommonRepository $commonRepository;
    protected ScribeService $scribeService;
    protected RedisService $redisService;

    public function __construct(LoggerInterface $logger
        ,RepairRepository $repairRepository
        ,UserRepository $userRepository
        ,FishingRepository $fishingRepository
        ,UpgradeRepository $upgradeRepository
        ,CommonRepository $commonRepository
        ,ScribeService $scribeService
        ,RedisService $redisService)
    {
        parent::__construct($logger);
        $this->repairRepository = $repairRepository;
        $this->userRepository = $userRepository;
        $this->fishingRepository = $fishingRepository;
        $this->upgradeRepository = $upgradeRepository;
        $this->commonRepository = $commonRepository;
        $this->scribeService = $scribeService;
        $this->redisService = $redisService;
    }
}