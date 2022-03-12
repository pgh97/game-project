<?php

namespace App\Application\Actions\Auth;

use App\Domain\Auth\Service\AccountInfoService;
use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class ModifyAuthAction extends AuthAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new AccountInfoService($this->logger ,$this->accountInfoRepository);
        $payload = array();
        $payload['accountInfo'] = $service->modifyAccountInfo($input);
        $this->logger->info("update account Action");
        return $this->respondWithData($payload);
    }
}