<?php

namespace App\Application\Actions\Auth;

use App\Domain\Auth\Service\AccountInfoService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class LoginAuthAction extends AuthAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new AccountInfoService($this->logger ,$this->accountInfoRepository);
        $accountInfo = $service->loginAccountInfo($input);
        $this->logger->info("login account info Action");

        if($accountInfo == 0){
            return $this->respondWithData($accountInfo, 401);
        }else{
            return $this->respondWithData($accountInfo);
        }
    }
}