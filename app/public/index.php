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

    require __DIR__ . '/../bootstrap.php';

    // Instantiate app
    $app = AppFactory::create();

    // Add Twig-View Middleware
    $twig = Twig::create(__DIR__ . '/../views/', ['cache' => false]);
    $app->add(TwigMiddleware::create($app, $twig));

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

    $app->get('/meu-bebe/{uuid}/painel', function (Request $request, Response $response, array $args) {
        // $response->getBody()->write('Hello World');
        // return $response;

        // get URI attribute
        $uuid = $args['uuid'];

        // create twig object
        $view = Twig::fromRequest($request);    

        $target = BabyModelDao::retrieveByUUID($uuid);

        // try {
        //     $db = new DB;
        //     $pdo = $db->connect();
        //     $sql = 'SELECT * FROM tb_babies WHERE uuid = :uuid';
        //     $stmt = $pdo->prepare($sql);
        //     $stmt->execute([
        //         ':uuid' => $uuid
        //     ]);
        //     $baby = $stmt->fetchAll(PDO::FETCH_OBJ);

        //     if(empty($baby)) {
        //         throw new AppException('Bebê não encontrado.');
        //     }

        //     $response = $view->render($response, 'bebe-dashboard.html', [
        //         'baby' => $baby[0]
        //     ]);

        // } catch(PDOException $e) {
        //     $response = $view->render($response, 'templates/error.html', [
        //         'message' => $e->getMessage()
        //     ]);
        // } catch(AppException $e) {
        //     $response = $view->render($response, 'templates/error.html', [
        //         'message' => $e->getMessage()
        //     ]);
        // }
        // finally {
        //     $db = null;
        // }

        $response = $view->render($response, 'bebe-dashboard.html', [
            'baby' => $target
        ]);

        return $response;
    })->setName('baby.dashboard');

    $app->get('/meu-bebe/{uuid}/editar', function (Request $request, Response $response, array $args) {
        
        // get URI argument
        $uuid = $args['uuid'];

        // create twig object
        $view = Twig::fromRequest($request);
        
        try {

            $target = BabyModelDao::retrieveByUUID($uuid);
            
            $response = $view->render($response, 'bebe-editar.html', [
                'baby' => $target
            ]);

        } catch(Exception $e) {
            $response = $view->render($response, 'templates/error.html', [
                'message' => $e->getMessage()
            ]);
        }

        // try {
        //     $db = new DB;
        //     $pdo = $db->connect();
        //     $sql = 'SELECT * FROM tb_babies WHERE uuid = :uuid';
        //     $stmt = $pdo->prepare($sql);
        //     $stmt->execute([
        //         ':uuid' => $uuid
        //     ]);
        //     $baby = $stmt->fetchAll(PDO::FETCH_OBJ);

        //     if(empty($baby)) {
        //         throw new AppException('Bebê não encontrado.');
        //     }

        //     $response = $view->render($response, 'bebe-editar.html', [
        //         'baby' => $baby[0]
        //     ]);

        // } catch(PDOException $e) {
        //     $response = $view->render($response, 'templates/error.html', [
        //         'message' => $e->getMessage()
        //     ]);
        // } catch(AppException $e) {
        //     $response = $view->render($response, 'templates/error.html', [
        //         'message' => $e->getMessage()
        //     ]);
        // }
        // finally {
        //     $db = null;
        // }

        return $response;
    })->setName('baby.edit');

    $app->post('/meu-bebe/{uuid}/editar', function (Request $request, Response $response, array $args) {
        // get URI attribute
        $uuid = $request->getAttribute('uuid');

        $params = $request->getParsedBody();

        switch($params['action']) {
            case 'update':
                $object = BabyModelDao::retrieveByUUID($uuid);
                $object->name = $params['name'];
                $object->description = $params['description'];
                $object->born_at = $params['born_at'];
                $object->gender = $params['gender'];
                $object = BabyModelDao::update($object);
                // redirect
                $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                $url = $routeParser->urlFor('baby.edit',['uuid'=>$uuid]);
                $response = $response->withHeader('Location', $url)->withStatus(302);
                break;
            case 'upload':
                $directory = UPLOAD_PATH;
                $uploadedFiles = $request->getUploadedFiles();

                // handle single input with single file upload
                $uploadedFile = $uploadedFiles['avatar'];
                if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                    $service = new FileUploadService($uploadedFile);
                    $service->setFilename($uuid);
                    $filename = $service->moveUploadedFileTo($directory);
                    $response->getBody()->write('Uploaded: ' . $filename . '<br/>');
                }
                break;
            default:
                break;
        }

        return $response;

        // try {
        //     $params = $request->getParsedBody();

        //     $object = BabyModelDao::retrieveByUUID($uuid);
        //     $object->name = $params['name'];
        //     $object->description = $params['description'];

        //     $object = BabyModelDao::update($object);

        //     // redirect
        //     $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        //     $url = $routeParser->urlFor('baby.edit',['uuid'=>$uuid]);
        //     return $response->withHeader('Location', $url)->withStatus(302);
        // } catch(Exception $e) {
        //     // create twig object
        //     $view = Twig::fromRequest($request);

        //     $response = $view->render($response, 'templates/error.html', [
        //         'message' => $e->getMessage()
        //     ]);
        // }

    })->setName('baby.edit');

    $app->get('/meu-bebe/{uuid}/remover', function (Request $request, Response $response, array $args) {

        // create twig object
        $view = Twig::fromRequest($request);    

        // get URI attribute
        $uuid = $request->getAttribute('uuid');

        $queryParams = $request->getQueryParams();
        switch($queryParams['action'] ?? null) {
            case 'no':
                $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                $url = $routeParser->urlFor('baby.dashboard',['uuid'=>$uuid]);
                return $response->withHeader('Location', $url)->withStatus(302);
                break;
            case 'yes':
                $rc = BabyModelDao::delete($uuid);
                $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                $url = $routeParser->urlFor('index',[]);
                return $response->withHeader('Location', $url)->withStatus(302);
                break;
            default:
                break;
        }        

        // Carregar a pagina
        try {
            $object = BabyModelDao::retrieveByUUID($uuid);
            if ($object === false)
                throw new AppException('Bebe nao encontrado.');

            $response = $view->render($response, 'bebe-remover.html', [
                'baby' => $object
            ]);
        } catch(Exception $e) {
            $response = $view->render($response, 'templates/error.html', [
                'message' => $e->getMessage()
            ]);
        }

        return $response;
    })->setName('baby.remove');

    $app->post('/meu-bebe/{uuid}/remover', function (Request $request, Response $response, array $args) {
        $params = $request->getParsedBody();
        var_dump($params);
    })->setName('baby.remove');

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

    