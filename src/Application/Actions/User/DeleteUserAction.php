<?php

namespace App\Application\Actions\User;

use App\Application\Actions\ActionError;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\User\Service\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class DeleteUserAction extends UserAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new UserService($this->logger, $this->userRepository
            , $this->commonRepository, $this->redisService);
        $payload=$service->removeUserInfo($input);
        if($payload==0){
            $this->logger->info("fail delete user info Action");
            $error = new ActionError("400",  ActionError::BAD_REQUEST, '캐릭터 삭제가 실패했습니다.');
            return $this->respondWithData(null, 400, $error);
        }else{
            $this->logger->info("delete user info Action");
            return $this->respondWithData($payload,200,null,"캐릭터가 삭제되었습니다.");
        }
    }
}