<?php

namespace App\Application\Actions\User;

use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\User\Service\UserInfoService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class GetWeatherInfoAction extends UserAction
{

    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new UserInfoService($this->logger, $this->userInfoRepository);
        return $this->respondWithData("");
    }
}