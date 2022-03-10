<?php

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Domain\Common\Service\RedisService;
use App\Domain\User\Repository\UserRepository;
use App\Exception\UserInfoException;
use Psr\Log\LoggerInterface;

abstract class UserAction extends Action
{
    protected UserRepository $userRepository;
    protected RedisService $redisService;

    public function __construct(LoggerInterface $logger
        , UserRepository                        $userRepository
        , RedisService                          $redisService)
    {
        parent::__construct($logger);
        $this->userRepository = $userRepository;
        $this->redisService = $redisService;
    }
}