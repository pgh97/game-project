<?php

namespace App\Application\Actions\Shop;

use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Shop\Service\ShopService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class GetShopAction extends ShopAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new ShopService($this->logger, $this->shopRepository
            ,$this->commonRepository, $this->redisService);
        $payload = array();
        return $this->respondWithData($payload);
    }
}