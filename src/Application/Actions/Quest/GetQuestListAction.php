<?php

namespace App\Application\Actions\Quest;

use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Quest\Service\QuestService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class GetQuestListAction extends QuestAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new QuestService($this->logger, $this->questRepository
            ,$this->commonRepository, $this->redisService);
        $payload = $service->getQuestInfoList($input);
        $this->logger->info("get list quest info Action");
        return $this->respondWithData($payload);
    }
}