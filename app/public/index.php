<?php

    use App\Dao\BabyModelDao;   
    use App\Exceptions\AppException;
    use App\Models\DB;
    use App\Services\FileUploadService;
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Selective\BasePath\BasePathMiddleware;
    use Slim\Views\Twig;
    use Slim\Views\TwigMiddleware;
    use Slim\Factory\AppFactory;
    use Slim\Routing\RouteCollectorProxy;
    use Slim\Routing\RouteContext;
    use DI\Container;

    require __DIR__ . '/../bootstrap.php';

    // Create Container using PHP-DI
    $container = new Container();

    $container->set(App\Controllers\BabyController::class, function () use ($container) {
        return new App\Controllers\BabyController($container);
    });

    $container->set(App\Controllers\AppointmentController::class, function () use ($container) {
        return new App\Controllers\AppointmentController($container);
    });

    $container->set('flash', function () {
        return new Slim\Flash\Messages();
    });

    // Instantiate app with container
    AppFactory::setContainer($container);
    $app = AppFactory::create();

    // Add Twig-View Middleware
    $twig = Twig::create(__DIR__ . '/../views/', ['cache' => false]);
    // Add global variables
    $environment = $twig->getEnvironment();
    //$environment->addGlobal('flash', $container->get(Messages::class));
    $environment->addGlobal('flash', $container->get('flash'));
    $app->add(TwigMiddleware::create($app, $twig));

    // Middlewares
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();
    $app->add(new BasePathMiddleware($app));
    
    // Add Error Handling Middleware
    $app->addErrorMiddleware(true, true, true);

    // Add route callbacks
    $app->get('/', function (Request $request, Response $response, array $args) {
        // $response->getBody()->write('Hello World');
        // return $response;

        $view = Twig::fromRequest($request);

        $babies = BabyModelDao::retrieveAll();
        // $sql = "SELECT * FROM tb_babies";
        // try {
        //     $db = new DB;
        //     $pdo = $db->connect();
        //     $stmt = $pdo->prepare($sql);
        //     $stmt->execute();
        //     $babies = $stmt->fetchAll(PDO::FETCH_OBJ);

        //     $response = $view->render($response, 'index.html', [
        //         'babies' => $babies
        //     ]);

        // } catch(PDOException $e) {
        //     $response = $view->render($response, 'templates/error.html', [
        //         'message' => $e->getMessage()
        //     ]);
        // } finally {
        //     $db = null;
        // }

        $response = $view->render($response, 'index.html', [
            'babies' => $babies
        ]);

        return $response;
    })->setName('index');

    $app->get('/adicionar', function (Request $request, Response $response, array $args) {
        // $response->getBody()->write('Hello World');
        // return $response;
        $view = Twig::fromRequest($request);
        return $view->render($response,'add.html');
    })->setName('add');

    $app->post('/adicionar', function (Request $request, Response $response, array $args) {
        // $response->getBody()->write('Hello World');
        // return $response;
        $view = Twig::fromRequest($request);
        return $view->render($response,'add.html');
    })->setName('add');

    $app->get('/meu-bebe/{uuid}/painel', App\Controllers\BabyController::class . ':dashboardGet')->setName('baby.dashboard');
    $app->get('/meu-bebe/{uuid}/editar', App\Controllers\BabyController::class . ':editGet')->setName('baby.edit');
    $app->post('/meu-bebe/{uuid}/editar', App\Controllers\BabyController::class . ':editPost')->setName('baby.edit');
    $app->post('/meu-bebe/{uuid}/remover', App\Controllers\BabyController::class . ':deletePost')->setName('baby.remove');
    

    $app->group('/api/v1/', function (RouteCollectorProxy $group) {        
        // Criar um novo baby
        $group->post('create', function ($request, $response, array $args) {
            
            $params = $request->getParsedBody();
            $object = BabyModelDao::create($params);
            $response->getBody()->write(json_encode($object));

            return $response->withHeader('content-type', 'application/json')->withStatus(200);
        })->setName('baby.create');

        // Remover um baby
        // $group->get('/{uuid}/delete', function($request, $response, array $args){
        //     $uuid = $request->getAttribute('uuid');
        //     $obj = BabyModelDao::retrieveByUUID($uuid);
            
        //     if($obj === false) {
        //         $response->getBody()->write('0');
        //         return $response->withHeader('content-type', 'application/json')->withStatus(404);
        //     }

        //     $rc = BabyModelDao::delete($obj);
        //     $response->getBody()->write(json_encode($rc));
        //     return $response->withHeader('content-type', 'application/json')->withStatus(200);
        // })->setName('baby.delete');

    });

    $app->get('/meu-bebe/{uuid}/compromissos',App\Controllers\AppointmentController::class . ':view')->setName('baby.appointments');

    // $app->get('/tasks', function (Request $request, Response $response, array $args) {
       
    //     $tasks = [];
    //     $sql = 'SELECT * FROM tasks';

    //     try {
    //         $db = new DB;
    //         $pdo = $db->connect();
    //         $stmt = $pdo->prepare($sql);
    //         $stmt->execute();
    //         $tasks = $stmt->fetchAll(PDO::FETCH_OBJ);

    //     } catch(PDOException $e) {
    //         $error = array(
    //             'message' => $e->getMessage()
    //         );
    //         $tasks = $error;
    //     } finally {
    //         $db = null;
    //     }
        
        
    //     $view = Twig::fromRequest($request);
    //     return $view->render($response, 'tasks.html', [
    //         'tasks' => $tasks
    //     ]);
    // });

    // $app->get('/task/all', function (Request $request, Response $response) {
    //     $sql = "SELECT * FROM tasks";
    //     try {
    //         $db = new DB;
    //         $pdo = $db->connect();
    //         $stmt = $pdo->prepare($sql);
    //         $stmt->execute();
    //         $tasks = $stmt->fetchAll(PDO::FETCH_OBJ);

    //         $response->getBody()->write(json_encode($tasks));
    //         $statusCode = 200;

    //     } catch(PDOException $e) {
    //         $error = array(
    //             'message' => $e->getMessage()
    //         );

    //         $response->getBody()->write(json_encode($error));
    //         $statusCode = 500;

    //     } finally {
    //         $db = null;
    //     }

    //     return $response->withHeader('content-type', 'application/json')->withStatus($statusCode);
    // });

    // $app->post('/task/add', function (Request $request, Response $response, array $args) {
        
    //     $data = $request->getParsedBody();
    //     $title = trim($data['title']);
        
    //     try {
            
    //         if (empty($title)) {
    //             throw new AppException('Invalid title.');
    //         }

    //         $db = new Db();
    //         $pdo = $db->connect();
    //         $stmt = $pdo->prepare("INSERT INTO tasks (title, done) VALUES ( :title , FALSE )");
    //         $result = $stmt->execute([
    //             ':title' => $title,
    //         ]);

    //         $response->getBody()->write(json_encode($result));
    //         $statusCode = 200;

    //     } catch(PDOException $e) {
    //         $error = array(
    //             "message" => $e->getMessage()
    //         );
           
    //         $response->getBody()->write(json_encode($error));
    //         $statusCode = 500;

    //     } catch (AppException $e) {
    //         $error = array(
    //             "message" => $e->getMessage()
    //         );
           
    //         $response->getBody()->write(json_encode($error));
    //         $statusCode = 500;
    //     } finally {
    //         $db = null;
    //     }

    //     return $response->withHeader('content-type', 'application/json')->withStatus($statusCode);

    // });

    // $app->get('/task/{id}/view',function(Request $request, Response $response, array $args){
        
    //     $id = $request->getAttribute('id');
        
    //     try {
    //         $db = new DB;
    //         $pdo = $db->connect();

    //         $sql = "SELECT * FROM tasks WHERE id = :id";

    //         $stmt = $pdo->prepare($sql);
    //         $stmt->execute([
    //             ':id' => $id
    //         ]);
    //         $customers = $stmt->fetchAll(PDO::FETCH_OBJ);

    //         $response->getBody()->write(json_encode($customers));
    //         $statusCode = 200;

    //     } catch (PDOException $e) {
    //         $error = array(
    //             'message' => $e->getMessage()
    //         );

    //         $response->getBody()->write(json_encode($error));
    //         $statusCode = 500;
    //     } finally {
    //         $db = null;
    //     }

    //     return $response->withHeader('content-type', 'application/json')->withStatus($statusCode);

    // });

    // $app->put('/task/{id}/toggle', function (Request $request, Response $response, array $args) {
        
    //     $id = $request->getAttribute('id');
        
    //     try {
    //         $db = new DB;
    //         $pdo = $db->connect();

    //         // update task
    //         $sql = 'UPDATE tasks SET done = !done WHERE id = :id';            
    //         $stmt = $pdo->prepare($sql);
    //         $result = $stmt->execute([
    //             ':id' => $id
    //         ]);
            
    //         $response->getBody()->write(json_encode($result));
    //         $statusCode = 200;

    //     } catch (PDOException $e) {
    //         $error = array(
    //             'message' => $e->getMessage()
    //         );

    //         $response->getBody()->write(json_encode($error));
    //         $statusCode = 500;

    //     } catch(AppException $e) {
    //         $error = array(
    //             'message' => $e->getMessage()
    //         );
    //         $response->getBody()->write(json_encode($error));
    //         $statusCode = 404;

    //     }
    //     finally {
    //         $db = null;
    //     }

    //     return $response->withHeader('content-type', 'application/json')->withStatus($statusCode);

    // });

    // $app->delete('/task/{id}/delete', function (Request $request, Response $response, array $args) {
        
    //     $id = $request->getAttribute('id');

    //     try {
    //         $db = new DB;
    //         $pdo = $db->connect();

    //         $sql = "DELETE FROM tasks WHERE id = :id";

    //         $stmt = $pdo->prepare($sql);
    //         $stmt->execute([
    //             ':id' => $id
    //         ]);

    //         $success = [
    //             'message' => true
    //         ];

    //         $response->getBody()->write(json_encode($success));
    //         $statusCode = 200;

    //     } catch (PDOException $e) {
    //         $error = array(
    //             'message' => $e->getMessage()
    //         );

    //         $response->getBody()->write(json_encode($error));
    //         $statusCode = 500;
    //     } finally {
    //         $db = null;
    //     }

    //     return $response->withHeader('content-type', 'application/json')->withStatus($statusCode);

    // });

    // $app->get('/uuid', function ($request, $response, $args){
    //     $uuid = UUID_v4();
    //     $response->getBody()->write();
    //     return $response;
    // });

    // $app->get('/hello/{name}', function ($request, $response, $args) {
    //     $view = Twig::fromRequest($request);
    //     return $view->render($response, 'hello.html', [
    //         'name' => $args['name']
    //     ]);
    // })->setName('hello');

    // Run application
    $app->run();

    