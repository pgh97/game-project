<?php

namespace App\Application\Actions\Upgrade;

use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Upgrade\Service\UpgradeService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class ModifyUpgradeShipItemActon extends UpgradeAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new UpgradeService($this->logger, $this->upgradeRepository, $this->userRepository, $this->fishingRepository
            ,$this->commonRepository, $this->redisService);
        $payload = $service->modifyUpgradeShipItem($input);
        $codeArray = $payload['codeArray'];
        unset($payload['codeArray']);
        $this->logger->info("upgrade ship item action");
        return $this->respondWithData($payload, 200, null, $codeArray);
    }
}