<?php

namespace App\Application\Actions\Auth;

use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class GetAuthAction extends AuthAction
{

    protected function action(Request $request, Response $response): Response
    {
        return $this->respondWithData("");
    }
}