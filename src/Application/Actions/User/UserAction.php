<?php

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\RedisService;
use App\Domain\User\Repository\UserRepository;
use App\Exception\UserInfoException;
use Psr\Log\LoggerInterface;

abstract class UserAction extends Action
{
    protected UserRepository $userRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;

    public function __construct(LoggerInterface $logger
        , UserRepository                        $userRepository
        , CommonRepository                      $commonRepository
        , RedisService                          $redisService)
    {
        parent::__construct($logger);
        $this->userRepository = $userRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }
}