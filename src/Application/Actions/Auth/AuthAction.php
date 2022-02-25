<?php
declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Application\Actions\Action;
use App\Domain\Auth\Repository\AccountInfoRepository;
use Psr\Log\LoggerInterface;

abstract class AuthAction extends Action
{
    protected AccountInfoRepository $accountInfoRepository;

    public function __construct(LoggerInterface $logger, AccountInfoRepository $accountInfoRepository)
    {
        parent::__construct($logger);
        $this->accountInfoRepository = $accountInfoRepository;
    }
}