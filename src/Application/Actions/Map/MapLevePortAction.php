<?php

namespace App\Application\Actions\Map;

use App\Application\Actions\ActionError;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Map\Service\MapService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class MapLevePortAction extends MapAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new MapService($this->logger, $this->mapRepository, $this->userRepository
            ,$this->auctionRepository ,$this->fishingRepository ,$this->questRepository
            ,$this->commonRepository, $this->redisService);
        $payload = $service->mapLevePort($input);
        if(array_filter($payload)){
            $this->logger->info("success leve port action");
            $message = $payload['message'];
            unset($payload['message']);
            return $this->respondWithData($payload, 200, null, $message);
        }else{
            $this->logger->info("fail leve port action");
            $error = new ActionError("400",  ActionError::BAD_REQUEST, '캐릭터의 피로도 또는 보로롱24 내구도가 부족합니다.');
            return $this->respondWithData(null, 401, $error);
        }
    }
}