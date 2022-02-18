<?php
namespace App\routes\library;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;

class LibraryRoute
{
    /**
     * Stores an instance of the Slim application.
     *
     * @var \Slim\App
     */
    private $app;
    public function __construct() {
        $app = AppFactory::create();
        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();
        $app->add(new BasePathMiddleware($app));
        $app->addErrorMiddleware(true, true, true);

        $app->get('/', function (Request $request, Response $response) {
            $response->getBody()->write("Welcome to the Adroit Library Demo.");
            return $response;
        });
        $app->group('/library', function () {
            $availableLibraryId = function ($id) {
                return (int)$id && $id > 0 && $id <= 5;
            };
            $this->map(['GET'], '', function (Request $request, Response $response) {
                return $response->withJson(['message' => 'Welcome, please pick a libray']);
            });
            $this->get('/{id}', function (Request $request, Response $response, $args) use ($availableLibraryId) {
                if($availableLibraryId($args['id'])) {
                    return $response->withJson(['message' => "library ".$args['id']]);
                }
                return $response->withJson(['message' => 'library Not Found'], 404);
            });
            $this->map(['POST', 'PUT', 'PATCH'], '/{id}', function (Request $request, Response $response, $args) use ($availableLibraryId) {
                if($availableLibraryId($args['id'])) {
                    return $response->withJson(['message' => "library ".$args['id']." updated successfully"]);
                }
                return $response->withJson(['message' => 'library Not Found'], 404);
            });
            $this->delete('/{id}', function (Request $request, Response $response, $args) use ($availableLibraryId) {
                if($availableLibraryId($args['id'])) {
                    return $response->withJson(['message' => "library ".$args['id']." deleted successfully"]);
                }
                return $response->withJson(['message' => 'library Not Found'], 404);
            });
        });
        $this->app = $app;
    }
    /**
     * Get an instance of the application.
     *
     * @return \Slim\App
     */
    public function get()
    {
        return $this->app;
    }
}