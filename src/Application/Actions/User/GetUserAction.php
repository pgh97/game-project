<?php

namespace App\Application\Actions\User;

use App\Domain\User\Service\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class GetUserAction extends UserAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new UserService($this->logger, $this->userRepository, $this->upgradeRepository
            , $this->fishingRepository, $this->commonRepository, $this->redisService);
        $userInfo = $service->getUserInfo($input);
        $payload = [
            'userInfo' => $userInfo
        ];
        $this->logger->info("get user info Action");
        return $this->respondWithData($payload);
    }
}