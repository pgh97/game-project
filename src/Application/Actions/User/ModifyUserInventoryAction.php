<?php

namespace App\Application\Actions\User;

use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class ModifyUserInventoryAction extends UserAction
{
    protected function action(Request $request, Response $response): Response
    {
        return $this->respondWithData("");
    }
}