<?php

namespace App\Application\Actions\User;

use App\Domain\User\Service\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class GetUserListAction extends UserAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new UserService($this->logger, $this->userRepository, $this->redisService);
        $userCode = $service->getUserInfoList($input);
        $this->logger->info("get List user info Action");
        return $this->respondWithData($userCode);
    }
}