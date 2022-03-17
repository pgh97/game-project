<?php

namespace App\Application\Actions\Fishing;

use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Fishing\Service\FishingService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class FishingOperateAction extends FishingAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new FishingService($this->logger, $this->fishingRepository, $this->userRepository
            ,$this->mapRepository ,$this->questRepository ,$this->commonRepository, $this->redisService);
        $result = $service->fishingOperate($input);
        $payload = array();
        $payload['fishInfo'] = $result['fishInfo'];
        return $this->respondWithData($payload,200,null,$result['message']);
    }
}