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
        $service = new UpgradeService($this->logger, $this->upgradeRepository
            ,$this->commonRepository, $this->redisService);
        $payload = array();
        return $this->respondWithData($payload);
    }
}