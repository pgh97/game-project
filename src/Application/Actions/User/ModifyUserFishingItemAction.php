<?php

namespace App\Application\Actions\User;

use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\User\Service\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class ModifyUserFishingItemAction extends UserAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new UserService($this->logger, $this->userRepository, $this->upgradeRepository
            , $this->fishingRepository,$this->commonRepository, $this->redisService);
        $payload = $service->modifyUserFishingItem($input);
        $this->logger->info("update user fishing-item action");
        $message = $payload['message'];
        unset($payload['message']);
        return $this->respondWithData($payload, 200, null, $message);
    }
}