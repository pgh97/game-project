<?php
namespace App\Application\Actions;

use App\Domain\Common\Service\ScribeService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

final class HomeAction
{
    private $container;
    protected LoggerInterface $logger;
    protected ScribeService $scribeService;

    public function __construct(ContainerInterface $container, LoggerInterface $logger
        , ScribeService $scribeService)
    {
        //$this->container = $container;
        $this->logger = $logger;
        $this->scribeService = $scribeService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $response->getBody()->write('public Hello, World!');
        $this->logger->info('public Hello, World!');
        $msg[] = new \LogEntry(array(
            'category' => 'login_log',
            'message' => 'public Hello, World!'
        ));
        $this->scribeService->Log($msg);
        return $response;
    }
}