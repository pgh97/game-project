<?php

namespace App\Application\Actions\Auth;

use App\Application\Actions\ActionError;
use App\Domain\Auth\Service\AccountInfoService;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Exception\ErrorCode;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class ModifyAuthAction extends AuthAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new AccountInfoService($this->logger ,$this->accountInfoRepository
            ,$this->scribeService, $this->redisService);
        $payload = $service->modifyAccountInfo($input);
        $codeArray = $payload['codeArray'];
        unset($payload['codeArray']);
        if(!empty($payload['accountInfo'])){
            $this->logger->info("update account Action");
            return $this->respondWithData($payload, 200, null, $codeArray);
        }else{
            $this->logger->info("fail update account info Action");
            $error = new ActionError($codeArray['statusCode'],  ErrorCode::BAD_REQUEST, $codeArray['message']);
            return $this->respondWithData(null, 200, $error);
        }
    }
}