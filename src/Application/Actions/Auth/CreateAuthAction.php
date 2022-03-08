<?php

namespace App\Application\Actions\Auth;

use App\Domain\Auth\Service\AccountInfoService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class CreateAuthAction extends AuthAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new AccountInfoService($this->logger ,$this->accountInfoRepository);
        $accountCode = $service->createAccountInfo($input);
        $payload = [
            'accountCode' => $accountCode
        ];
        $this->logger->info("create account info Action");
        return $this->respondWithData($payload);
    }
}