<?php

namespace App\Application\Actions\Map;

use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Map\Service\MapService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class MapEnterPortAction extends MapAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new MapService($this->logger, $this->mapRepository, $this->userRepository
            ,$this->auctionRepository ,$this->fishingRepository ,$this->commonRepository, $this->redisService);
        $payload = $service->mapEnterPort($input);
        $this->logger->info("success enter port action");
        $message = $payload['message'];
        unset($payload['message']);
        return $this->respondWithData($payload, 200, null, $message);
    }
}