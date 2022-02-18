<?php
use App\config\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';
//include_once __DIR__ ."/../src/config/db_connection.php";

$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->add(new BasePathMiddleware($app));
$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write('public Hello World!');
    return $response;
});

$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");
    return $response;
});

$app->get('/fish-info-data/all', function (Request $request, Response $response) {
    $sql = "SELECT * FROM fish_info_data";

    try {
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->query($sql);
        $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        $response->getBody()->write(json_encode($customers));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// Customer Routes
//require_once '../app/routes/customers.php';

//function availableLibraryId($id) {
//    return (int)$id && $id > 0 && $id <= 5;
//}

//$app->group('/library', function () {
//
//    $this->map(['GET'], '', function (Request $request, Response $response) {
//        return $response->withJson(['message' => 'Welcome, please pick a libray']);
//    });
//    $this->get('/{id}', function (Request $request, Response $response, $args) {
//        if(availableLibraryId($args['id'])) {
//            return $response->withJson(['message' => "library ".$args['id']]);
//        }
//        return $response->withJson(['message' => 'library Not Found'], 404);
//    });
//    $this->map(['POST', 'PUT', 'PATCH'], '/{id}', function (Request $request, Response $response, $args) {
//        if(availableLibraryId($args['id'])) {
//            return $response->withJson(['message' => "library ".$args['id']." updated successfully"]);
//        }
//        return $response->withJson(['message' => 'library Not Found'], 404);
//    });
//    $this->delete('/{id}', function (Request $request, Response $response, $args) {
//        if(availableLibraryId($args['id'])) {
//            return $response->withJson(['message' => "library ".$args['id']." deleted successfully"]);
//        }
//        return $response->withJson(['message' => 'library Not Found'], 404);
//    });
//});

// Run app
//$app = (new App\routes\library\LibraryRoute())->get();

$app->run();