<?php

namespace App\Domain\Repair\Service;

use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\Repair\Repository\RepairRepository;
use App\Domain\User\Repository\UserRepository;
use Psr\Log\LoggerInterface;

class RepairService extends BaseService
{
    protected RepairRepository $repairRepository;
    protected UserRepository $userRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

    private const REPAIR_REDIS_KEY = 'repair:%s';

    public function __construct(LoggerInterface $logger
        ,RepairRepository $repairRepository
        ,UserRepository $userRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->repairRepository = $repairRepository;
        $this->userRepository = $userRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }
    
    public function modifyRepairItem(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        return [
            'message' => "테스트",
        ];
    }
    
    public function modifyRepairUser(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        return [
            'message' => "테스트",
        ];
    }
}