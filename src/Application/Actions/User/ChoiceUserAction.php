<?php

namespace App\Application\Actions\User;

use App\Application\Actions\ActionError;
use App\Domain\User\Service\UserService;
use App\Exception\ErrorCode;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class ChoiceUserAction extends UserAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new UserService($this->logger, $this->userRepository, $this->upgradeRepository
            , $this->fishingRepository, $this->commonRepository, $this->scribeService, $this->redisService);
        $payload = $service->getUserInfoChoice($input);
        $codeArray = $payload['codeArray'];
        unset($payload['codeArray']);
        if(empty($payload)){
            $this->logger->info("NOT FOUNT USER INFO");
            $error = new ActionError($codeArray['statusCode'],  ErrorCode::BAD_REQUEST, $codeArray['message']);
            return $this->respondWithData(null, 200, $error);
        }else{
            $this->logger->info("get choice user info Action");
            return $this->respondWithData($payload, 200, null, $codeArray);
        }
    }
}