<?php
declare(strict_types=1);

//use App\Application\Actions\User\ListUsersAction;
//use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
//    $app->options('/{routes:.*}', function (Request $request, Response $response) {
//        // CORS Pre-Flight OPTIONS Request Handler
//        return $response;
//    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('public Hello World!');
        return $response;
    });
//
//    $app->group('/users', function (Group $group) {
//        $group->get('', ListUsersAction::class);
//        $group->get('/{id}', ViewUserAction::class);
//    });

    $app->get('/fishs', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM fish_info_data");
        $sth->execute();
        $data = $sth->fetchAll(PDO::FETCH_ASSOC);
        $payload = json_encode($data);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    });

//    $app->post('/tasks', function (Request $request, Response $response) {
//        $db = $this->get(PDO::class);
//        $contents = json_decode(file_get_contents('php://input'), true);
//
//        foreach ($contents as $item) {
//            $sth = $db->prepare("
//            INSERT INTO `tasks` (
//                `title`, `start_date`, `due_date`,
//                `status`, `priority`,  `description`,
//                `created_at`
//            ) VALUES (
//                 :title,  CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,
//                 :status, :priority,         :description,
//                 CURRENT_TIMESTAMP
//            )");
//
//            $sth->bindParam(':title', $item['title']);
//            $sth->bindParam(':status', $item['status']);
//            $sth->bindParam(':priority', $item['priority']);
//            $sth->bindParam(':description', $item['description']);
//            $sth->execute();
//        }
//
//        $response->getBody()->write( json_encode($contents));
//        return $response->withHeader('Content-Type', 'application/json');
//    });
};
