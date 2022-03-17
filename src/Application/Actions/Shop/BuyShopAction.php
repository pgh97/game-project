<?php

namespace App\Application\Actions\Shop;

use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Shop\Service\ShopService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class BuyShopAction extends ShopAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new ShopService($this->logger, $this->shopRepository, $this->userRepository
            ,$this->commonRepository, $this->redisService);
        $payload = $service->buyShopInfo($input);
        $this->logger->info("buy shop info action");
        $message = $payload['message'];
        unset($payload['message']);
        return $this->respondWithData($payload, 200, null, $message);
    }
}