<?php

namespace App\Application\Actions\User;

use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\User\Service\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class GetUserInventoryAction extends UserAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new UserService($this->logger, $this->userRepository
            ,$this->commonRepository, $this->redisService);
        $payload = array();
        $payload['userInventory'] = $service->getUserInventory($input);
        $this->logger->info("get user inventory Action");
        return $this->respondWithData($payload);
    }
}