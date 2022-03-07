<?php

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Domain\User\Repository\UserInfoRepository;
use App\Exception\UserInfoException;
use Psr\Log\LoggerInterface;

abstract class UserAction extends Action
{
    protected UserInfoRepository $userInfoRepository;

    public function __construct(LoggerInterface $logger, UserInfoRepository $userInfoRepository)
    {
        parent::__construct($logger);
        $this->userInfoRepository = $userInfoRepository;
    }

    protected function checkUserPermissions(int $userId, int $userIdLogged): void
    {
        if ($userId !== $userIdLogged) {
            throw new UserInfoException('User permission failed.', 400);
        }
    }

    protected function getAndValidateUsers(array $input): object
    {
        if (isset($input['decoded']) && isset($input['decoded']->data)) {
            return $input['decoded']->data;
        }

        throw new UserInfoException('Invalid user. Permission failed.', 400);
    }
}