<?php

namespace App\Application\Middleware;

use App\Exception\UserInfoException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTAuthMiddleware
{
    public function __invoke(
        Request $request
        , RequestHandler $handler
    ): Response {
        $jwtHeader = $request->getHeaderLine('Authorization');
        if(! $jwtHeader) {
            throw new UserInfoException("400", ActionError::BAD_REQUEST, 'JWT Token required');
        }
        //$jwt = explode('Bearer ', $jwtHeader);
        $jwt = str_replace('Bearer ', '', $jwtHeader);

        if(! isset($jwt)){
            throw new UserInfoException("400", ActionError::BAD_REQUEST, 'JWT Token invalid');
        }

        $decoded = null;
        try {
            $decoded = JWT::decode($jwt, new Key($_SERVER['SECRET_KEY'], 'HS256'));
        } catch (\UnexpectedValueException $exception) {
            throw new UserInfoException('Forbidden: you are not authorized.', 403);
        }

        $object = (array) $request->getParsedBody();
        $object['decoded'] = $decoded;
        return $handler->handle($request->withParsedBody($object));
    }
}