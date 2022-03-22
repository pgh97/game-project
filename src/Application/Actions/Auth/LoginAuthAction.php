<?php

namespace App\Application\Actions\Auth;

use App\Application\Actions\ActionError;
use App\Domain\Auth\Service\AccountInfoService;
use App\Exception\ErrorCode;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class LoginAuthAction extends AuthAction
{
    /**
     * @throws \Exception
     */
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new AccountInfoService($this->logger ,$this->accountInfoRepository
            ,$this->scribeService, $this->redisService);
        $payload = $service->loginAccountInfo($input);
        $codeArray = $payload['codeArray'];
        unset($payload['codeArray']);
        if(empty($payload)){
            $this->logger->info("fail login account info Action");
            $error = new ActionError($codeArray['statusCode'],  ErrorCode::UNAUTHENTICATED, $codeArray['message']);
            return $this->respondWithData(null, 200, $error);
        }else{
            $this->logger->info("login account info Action");
            return $this->respondWithData($payload, 200, null, $codeArray);
        }
    }
}