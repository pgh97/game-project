<?php

namespace App\Application\Actions\Repair;

use App\Application\Actions\Action;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\RedisService;
use App\Domain\Fishing\Repository\FishingRepository;
use App\Domain\Repair\Repository\RepairRepository;
use App\Domain\User\Repository\UserRepository;
use Psr\Log\LoggerInterface;

abstract class RepairAction extends Action
{
    protected RepairRepository $repairRepository;
    protected UserRepository $userRepository;
    protected FishingRepository $fishingRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;

    public function __construct(LoggerInterface $logger
        ,RepairRepository $repairRepository
        ,UserRepository $userRepository
        ,FishingRepository $fishingRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        parent::__construct($logger);
        $this->repairRepository = $repairRepository;
        $this->userRepository = $userRepository;
        $this->fishingRepository = $fishingRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }
}