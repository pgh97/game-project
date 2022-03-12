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
        $service = new AccountInfoService($this->logger ,$this->accountInfoRepository);
        $payload = $service->removeAccountInfo($input);
        if($payload==0){
            $this->logger->info("fail delete account info Action");
            $error = new ActionError("400",  ActionError::BAD_REQUEST, '회원탈퇴가 실패했습니다.');
            return $this->respondWithData(null, 400, $error);
        }else{
            $this->logger->info("delete account info Action");
            return $this->respondWithData($payload,200,null,"회원탈퇴가 되었습니다.");
        }
    }
}