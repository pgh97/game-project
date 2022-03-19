<?php

namespace App\Application\Actions\User;

use App\Application\Actions\ActionError;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\User\Service\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class DeleteUserInventoryAction extends UserAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new UserService($this->logger, $this->userRepository, $this->upgradeRepository
            , $this->fishingRepository, $this->commonRepository, $this->redisService);
        $payload=$service->removeUserInventory($input);
        if($payload==0){
            $this->logger->info("fail delete user inventory Action");
            $error = new ActionError("400",  ActionError::BAD_REQUEST, '인벤토리 아이템 삭제 실패했습니다.');
            return $this->respondWithData(null, 400, $error);
        }else{
            $this->logger->info("delete user inventory Action");
            return $this->respondWithData($payload,200,null,"인벤토리 아이템이 삭제되었습니다.");
        }
    }
}