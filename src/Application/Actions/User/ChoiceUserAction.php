<?php

namespace App\Application\Actions\User;

use App\Application\Actions\ActionError;
use App\Domain\User\Service\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class ChoiceUserAction extends UserAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new UserService($this->logger, $this->userRepository
            , $this->commonRepository, $this->redisService);
        $payload = $service->getUserInfoChoice($input);
        if(empty($payload)){
            $this->logger->info("NOT FOUNT USER INFO");
            $error = new ActionError("400",  ActionError::UNAUTHENTICATED, '잘못된 요청입니다.');
            return $this->respondWithData(null, 401, $error);
        }else{
            $this->logger->info("get choice user info Action");
            return $this->respondWithData($payload);
        }
    }
}