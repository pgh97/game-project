<?php

namespace App\Application\Actions\User;

use App\Application\Actions\ActionError;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\User\Service\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class CreateUserFishingItemAction extends UserAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new UserService($this->logger, $this->userRepository, $this->upgradeRepository
            , $this->fishingRepository, $this->commonRepository, $this->redisService);
        $choiceCode = $service->createUserFishingItem($input);
        $payload = array();

        if($choiceCode==0){
            $this->logger->info("fail create user fishing-item Action");
            $error = new ActionError("400",  ActionError::BAD_REQUEST, '채비 저장 횟수 초과했습니다.');
            return $this->respondWithData(null, 400, $error);
        }else{
            $payload['choiceCode'] = $choiceCode;
            $this->logger->info("create user fishing-item Action");
            return $this->respondWithData($payload);
        }
    }
}