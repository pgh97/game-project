<?php

namespace App\Application\Actions\Auth;

use App\Application\Actions\ActionError;
use App\Domain\Auth\Service\AccountInfoService;
use App\Exception\ErrorCode;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class CreateAuthAction extends AuthAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new AccountInfoService($this->logger ,$this->accountInfoRepository
            ,$this->scribeService, $this->redisService);
        $payload = $service->createAccountInfo($input);
        $codeArray = $payload['codeArray'];
        unset($payload['codeArray']);
        if(!empty($payload['accountCode'])){
            $this->logger->info("create account info Action");
            return $this->respondWithData($payload, 200, null, $codeArray);
        }else{
            $this->logger->info("fail create account info Action");
            $error = new ActionError($codeArray['statusCode'],  ErrorCode::BAD_REQUEST, $codeArray['message']);
            return $this->respondWithData(null, 200, $error);
        }
    }
}