<?php

namespace App\Application\Actions\Map;

use App\Application\Actions\ActionError;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Map\Service\MapService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class ModifyShipAction extends MapAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new MapService($this->logger, $this->mapRepository, $this->userRepository
            ,$this->auctionRepository ,$this->fishingRepository ,$this->commonRepository, $this->redisService);
        $payload = array();
        $payload['userShipInfo'] = $service->modifyShipDurability($input);
        if(array_filter($payload)){
            $this->logger->info("map ship durability action");
            return $this->respondWithData($payload);
        }else{
            $this->logger->info("fail ship durability action");
            $error = new ActionError("400",  ActionError::BAD_REQUEST, '보로롱24 내구도가 0입니다. 항구로 입항합니다.');
            return $this->respondWithData(null, 401, $error);
        }
    }
}