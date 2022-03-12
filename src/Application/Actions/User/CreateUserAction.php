<?php
declare(strict_types=1);
namespace App\Application\Actions\User;

use App\Domain\User\Service\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CreateUserAction extends UserAction
{
    protected function action(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $service = new UserService($this->logger, $this->userRepository
            , $this->commonRepository, $this->redisService);
        $payload = array();
        $payload['userCode'] = $service->createUserInfo($input);
        $this->logger->info("create user info Action");
        return $this->respondWithData($payload);
    }
}