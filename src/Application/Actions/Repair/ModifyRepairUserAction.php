<?php

namespace App\Application\Actions\Repair;

use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Repair\Service\RepairService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class ModifyRepairUserAction extends RepairAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new RepairService($this->logger, $this->repairRepository, $this->userRepository
            ,$this->fishingRepository ,$this->commonRepository, $this->redisService);
        $payload = $service->modifyRepairUser($input);
        $this->logger->info("update user info repair action");
        $message = $payload['message'];
        unset($payload['message']);
        return $this->respondWithData($payload, 200, null, $message);
    }
}