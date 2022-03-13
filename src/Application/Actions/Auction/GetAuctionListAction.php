<?php

namespace App\Application\Actions\Auction;

use App\Domain\Auction\Service\AuctionService;
use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class GetAuctionListAction extends AuctionAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new AuctionService($this->logger, $this->auctionRepository
                ,$this->commonRepository, $this->redisService);
        $payload = array();
        return $this->respondWithData($payload);
    }
}