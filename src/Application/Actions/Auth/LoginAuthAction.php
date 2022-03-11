<?php

namespace App\Application\Actions\Auth;

use App\Application\Actions\ActionError;
use App\Domain\Auth\Service\AccountInfoService;
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
        $service = new AccountInfoService($this->logger ,$this->accountInfoRepository);
        $payload = $service->loginAccountInfo($input);

        if(empty($payload)){
            $this->logger->info("fail account info Action");
            $error = new ActionError("401",  ActionError::UNAUTHENTICATED, '인증체크 실패입니다.');
            return $this->respondWithData(null, 401, $error);
        }else{
            $this->logger->info("login account info Action");
            return $this->respondWithData($payload);
        }
    }
}