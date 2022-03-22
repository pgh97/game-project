<?php
declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Application\Actions\Action;
use App\Domain\Auth\Repository\AccountInfoRepository;
use App\Domain\Common\Service\RedisService;
use App\Domain\Common\Service\ScribeService;
use Psr\Log\LoggerInterface;

abstract class AuthAction extends Action
{
    protected AccountInfoRepository $accountInfoRepository;
    protected ScribeService $scribeService;
    protected RedisService $redisService;

    public function __construct(LoggerInterface $logger, AccountInfoRepository $accountInfoRepository
        , ScribeService $scribeService
        , RedisService $redisService)
    {
        parent::__construct($logger);
        $this->accountInfoRepository = $accountInfoRepository;
        $this->scribeService = $scribeService;
        $this->redisService = $redisService;
    }
}