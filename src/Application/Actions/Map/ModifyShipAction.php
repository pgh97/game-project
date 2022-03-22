<?php

namespace App\Application\Actions\Map;

use App\Application\Actions\ActionError;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Map\Service\MapService;
use App\Exception\ErrorCode;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class ModifyShipAction extends MapAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new MapService($this->logger, $this->mapRepository, $this->userRepository
            ,$this->auctionRepository ,$this->fishingRepository ,$this->questRepository
            ,$this->commonRepository, $this->redisService);
        $payload = $service->modifyShipDurability($input);
        $codeArray = $payload['codeArray'];
        unset($payload['codeArray']);
        if(!empty($payload['userShipInfo'])){
            $this->logger->info("map ship durability action");
            return $this->respondWithData($payload, 200, null, $codeArray);
        }else{
            $this->logger->info("fail ship durability action");
            $error = new ActionError($codeArray['statusCode'],  ErrorCode::NOT_FULL_SHIP, $codeArray['message']);
            return $this->respondWithData(null, 200, $error);
        }
    }
}