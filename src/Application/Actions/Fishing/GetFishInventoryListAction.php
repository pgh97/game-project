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
        $service = new FishingService($this->logger ,$this->fishingRepository ,$this->userRepository
            ,$this->mapRepository ,$this->questRepository ,$this->upgradeRepository ,$this->commonRepository, $this->redisService);
        $payload = $service->getFishInventoryList($input);
        $this->logger->info("get list fish inventory info Action");
        return $this->respondWithData($payload);
    }
}