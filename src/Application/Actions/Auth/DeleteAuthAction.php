<?php

namespace App\Application\Actions\Auth;

use App\Application\Actions\ActionError;
use App\Domain\Auth\Service\AccountInfoService;
use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class DeleteAuthAction extends AuthAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new AccountInfoService($this->logger ,$this->accountInfoRepository
            ,$this->scribeService, $this->redisService);
        $payload = $service->removeAccountInfo($input);
        $codeArray = $payload['codeArray'];
        unset($payload['codeArray']);
        return $this->respondWithData($payload, 200, null, $codeArray);
    }
}