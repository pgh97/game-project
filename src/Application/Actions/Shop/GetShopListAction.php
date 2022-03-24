<?php

namespace App\Application\Actions\Shop;

use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Shop\Service\ShopService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class GetShopListAction extends ShopAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new ShopService($this->logger, $this->shopRepository, $this->userRepository
            ,$this->commonRepository, $this->scribeService, $this->redisService);
        $payload = $service->getShopInfoList($input);
        $codeArray = $payload['codeArray'];
        unset($payload['codeArray']);
        $this->logger->info("get list shop info Action");
        return $this->respondWithData($payload, 200, null, $codeArray);
    }
}