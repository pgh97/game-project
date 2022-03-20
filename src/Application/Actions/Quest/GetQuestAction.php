<?php

namespace App\Application\Actions\Quest;

use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Quest\Service\QuestService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class GetQuestAction extends QuestAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new QuestService($this->logger, $this->questRepository
            ,$this->commonRepository, $this->redisService);
        $payload = array();
        $payload['questInfo'] = $service->getQuestInfo($input);
        $this->logger->info("get quest info Action");
        return $this->respondWithData($payload);
    }
}