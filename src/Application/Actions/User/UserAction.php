<?php

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Domain\User\Repository\UserInfoRepository;
use Psr\Log\LoggerInterface;

abstract class UserAction extends Action
{
    protected UserInfoRepository $userInfoRepository;

    public function __construct(LoggerInterface $logger, UserInfoRepository $userInfoRepository)
    {
        parent::__construct($logger);
        $this->userInfoRepository = $userInfoRepository;
    }
}