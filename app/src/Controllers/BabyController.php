<?php

    namespace App\Controllers;

    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Server\RequestHandlerInterface;
    use Psr\Container\ContainerInterface;
    use Psr\Log\LoggerInterface;
    use Slim\Psr7\Response;
    use Slim\Views\Twig;
    use Slim\Routing\RouteContext;

    // class HomeController implements RequestHandlerInterface
    class BabyController
    {
        private $container;

        public function __construct(ContainerInterface $container)
        {
                $this->container = $container;
        }

        public function dashboardGet(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface
        {
            // get URI argument
            $uuid = $args['uuid'];

            // get TWIG
            $view = Twig::fromRequest($request);

            try {
                $target = \App\Dao\BabyModelDao::retrieveByUUID($uuid);
                $response = $view->render($response,'bebe-dashboard.html',[
                    'baby' => $target
                ]);
            } catch (\Exception $e) {
                $response = $view->render($response, 'templates/error.html', [
                    'message' => $e->getMessage()
                ]);
            }

            return $response;
        }

        public function editGet(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface
        {
            // get URI argument
            $uuid = $args['uuid'];

            // get TWIG
            $view = Twig::fromRequest($request);

            try {
                $target = \App\Dao\BabyModelDao::retrieveByUUID($uuid);
                $response = $view->render($response,'bebe-editar.html',[
                    'baby' => $target
                ]);
            } catch (\Exception $e) {
                $response = $view->render($response, 'templates/error.html', [
                    'message' => $e->getMessage()
                ]);
            }

            return $response;
        }

        public function editPost(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface
        {
            // get UUID
            $uuid = $args['uuid'];

            // get TWIG
            $view = Twig::fromRequest($request);
            
            // get Flash
            $flash = $this->container->get('flash');

            // get form params
            $params = $request->getParsedBody();

            try {
                switch($params['action']) {
                    case 'update':
                        $target = \App\Dao\BabyModelDao::retrieveByUUID($uuid);
                        $target->name = $params['name'];
                        $target->description = $params['description'];
                        $target->born_at = $params['born_at'];
                        $target->gender = $params['gender'];
                        $target = \App\Dao\BabyModelDao::update($target);
                        $flash->addMessage('success', 'Dados atualizados com sucesso.');                        
                        break;
                    default:
                        break;
                } 
            } catch (\Exception $e) {
                    // $response = $view->render($response, 'templates/error.html', [
                    //     'message' => $e->getMessage()
                    // ]);
                    $flash->addMessage('error', 'Falha ao atualizar os dados.'); 
            }
            
            // redirect
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $url = $routeParser->urlFor('baby.edit',['uuid'=>$uuid]);
            $response = $response->withHeader('Location', $url)->withStatus(302);

            return $response;

            // switch($params['action']) {
            //         case 'update':
                            
            //                 // redirect
            //                 $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            //                 $url = $routeParser->urlFor('baby.edit',['uuid'=>$uuid]);
            //                 $response = $response->withHeader('Location', $url)->withStatus(302);
            //                 break;
            //         case 'upload':
            //                 $directory = UPLOAD_PATH;
            //                 $uploadedFiles = $request->getUploadedFiles();
            
            //                 // handle single input with single file upload
            //                 $uploadedFile = $uploadedFiles['avatar'];
            //                 if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            //                 $service = new FileUploadService($uploadedFile);
            //                 $service->setFilename($uuid);
            //                 $filename = $service->moveUploadedFileTo($directory);
            //                 $response->getBody()->write('Uploaded: ' . $filename . '<br/>');
            //                 }
            //                 break;
            //         default:
            //                 break;
            // }
        
            

        }

        public function deletePost(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface
        {
            // get UUID
            $uuid = $args['uuid'];

            // get TWIG
            $view = Twig::fromRequest($request);

            // get form params
            $params = $request->getParsedBody();

            try {
                
                \App\Dao\BabyModelDao::delete($uuid);
                // redirect
                $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                $url = $routeParser->urlFor('index');
                $response = $response->withHeader('Location', $url)->withStatus(302);
            
            } catch (\Exception $e) {
                $response = $view->render($response, 'templates/error.html', [
                    'message' => $e->getMessage()
                ]);
            }
            return $response;
        }
    }