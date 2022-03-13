<?php

namespace App\Application\Actions\Fishing;

use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Fishing\Service\FishingService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class GetFishInventoryListAction extends FishingAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new FishingService($this->logger, $this->fishingRepository
            ,$this->commonRepository, $this->redisService);
        $payload = array();
        return $this->respondWithData($payload);
    }
}