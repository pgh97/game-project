<?php

namespace App\Application\Actions\Repair;

use App\Application\Actions\Action;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\RedisService;
use App\Domain\Repair\Repository\RepairRepository;
use Psr\Log\LoggerInterface;

abstract class RepairAction extends Action
{
    protected RepairRepository $repairRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;

    public function __construct(LoggerInterface $logger
        ,RepairRepository $repairRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        parent::__construct($logger);
        $this->repairRepository = $repairRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }
}