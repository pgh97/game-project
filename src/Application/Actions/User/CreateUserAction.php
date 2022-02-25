<?php

namespace App\Application\Actions\User;

use App\Application\Actions\ActionError;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Firebase\JWT\JWT;

class CreateUserAction extends UserAction
{

    protected function action(Request $request, Response $response): Response
    {
        if ($request->hasHeader('Authorization')) {
            $authorization = $request->getHeaderLine('Authorization');
            $jwt = str_replace('Bearer ', '', $authorization);
            $this->logger->info("create user info Action");
            $decoded = null;
            $decoded_array = array();
            $object = (array) $request->getParsedBody();
            try {
                $decoded = JWT::decode($jwt, $_SERVER['SECRET_KEY'], ['HS256']);
                $decoded_array = (array)$decoded;
            } catch (\UnexpectedValueException $exception) {

            }

            $object['decoded'] = $decoded;
            return $this->respondWithData($object);
        }else{
            $this->logger->info("fail create user info Action");
            $error = new ActionError(401,  ActionError::UNAUTHENTICATED, '인증체크 실패입니다.');
            return $this->respondWithData(null, 401, $error, );
        }
    }
}