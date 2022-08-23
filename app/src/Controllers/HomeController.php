<?php

        namespace App\Controllers;

        use Psr\Http\Message\ResponseInterface;
        use Psr\Http\Message\ServerRequestInterface;
        use Psr\Http\Server\RequestHandlerInterface;
        use Psr\Log\LoggerInterface;
        use Slim\Psr7\Response;
        use Psr\Container\ContainerInterface;

        // class HomeController implements RequestHandlerInterface
        class HomeController
        {
                private $container;

                public function __construct(ContainerInterface $container)
                {
                        $this->container = $container;
                }

                public function hello(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface
                {
                        $name = $args['name'];

                        $response->getBody()->write("Hello, $name!");
                        return $response;
                }
        }