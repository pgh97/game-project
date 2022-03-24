<?php

namespace App\Application\Actions\Auction;

use App\Domain\Auction\Service\AuctionService;
use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class SellAuctionAction extends AuctionAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new AuctionService($this->logger, $this->auctionRepository
            ,$this->userRepository ,$this->commonRepository, $this->scribeService, $this->redisService);
        $payload = $service->sellAuctionItem($input);
        $codeArray = $payload['codeArray'];
        unset($payload['codeArray']);
        $this->logger->info("success sell auction item action");
        return $this->respondWithData($payload, 200, null, $codeArray);
    }
}