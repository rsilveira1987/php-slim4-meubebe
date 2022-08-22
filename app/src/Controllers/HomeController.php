<?php

        namespace App\Controllers;

        use Psr\Http\Message\ResponseInterface;
        use Psr\Http\Message\ServerRequestInterface;
        use Psr\Http\Server\RequestHandlerInterface;
        use Psr\Log\LoggerInterface;
        use Slim\Psr7\Response;

        // class HomeController implements RequestHandlerInterface
        class HomeController
        {
                // private $logger;

                // public function __construct(LoggerInterface $logger)
                // {
                //         $this->logger = $logger;
                // }

                public function hello(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface
                {
                        $name = $args['name'];

                        // $name = $request->getAttribute('name', 'world');
                        // $response = new Response();
                        $response->getBody()->write("Hello, $name!");
                        return $response;
                }
        }