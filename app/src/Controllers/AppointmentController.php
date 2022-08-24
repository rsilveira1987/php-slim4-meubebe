<?php

    namespace App\Controllers;

    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Server\RequestHandlerInterface;
    use Psr\Container\ContainerInterface;
    use Slim\Psr7\Response;
    use Slim\Views\Twig;

    // class HomeController implements RequestHandlerInterface
    class AppointmentController
    {
        private $container;

        public function __construct(ContainerInterface $container)
        {
                $this->container = $container;
        }

        public function view(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface
        {
            // get URI argument
            $uuid = $args['uuid'];

            // get TWIG
            $view = Twig::fromRequest($request);

            try {
                $target = \App\Dao\BabyModelDao::retrieveByUUID($uuid);
                $response = $view->render($response,'bebe-compromissos.html',[
                    'baby' => $target
                ]);
            } catch (Exception $e) {
                $response = $view->render($response, 'templates/error.html', [
                    'message' => $e->getMessage()
                ]);
            }

            return $response;
        }
    }