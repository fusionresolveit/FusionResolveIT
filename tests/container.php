<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return [
  'settings' => [],
  App::class => function (ContainerInterface $container)
  {
    $app = AppFactory::createFromContainer($container);

    \App\Route::setRoutes($app);

    $twig = Twig::create(__DIR__ . '/../src/v1/Views');
    $app->add(new TwigMiddleware($twig, $app->getRouteCollector()->getRouteParser(), '', 'view'));

    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();
    // $app->addErrorMiddleware(false, true, false);


    // Define Custom Error Handler
    $customErrorHandler = function (
      $request,
      Throwable $exception,
      bool $displayErrorDetails,
      bool $logErrors,
      bool $logErrorDetails
    ) use ($app)
    {
      global $basePath;

      if ($exception->getCode() == 401)
      {
        $view = Twig::create(__DIR__ . '/../src/v1/Views');

        $response = $app->getResponseFactory()->createResponse()->withStatus($exception->getCode());
        $viewData = [
          'rootpath' => $basePath,
          'message'  => $exception->getMessage(),
        ];

        return $view->render($response, 'error401.html.twig', $viewData);
      } else {
        $response = $app->getResponseFactory()->createResponse();
        $error = [
          "status"  => "error",
          "message" => $exception->getMessage()
        ];
        echo $exception->getMessage();
        print_r($exception->getTraceAsString());
        $code = 500;
        if ($exception->getCode() >= 300) {
          $code = $exception->getCode();
        }
        $response->getBody()->write(json_encode($error));
        return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
      }
    };

    $errorMiddleware = $app->addErrorMiddleware(true, true, true);
    $errorMiddleware->setDefaultErrorHandler($customErrorHandler);

    return $app;
  },

  ServerRequestFactoryInterface::class => function (ContainerInterface $container)
  {
    return $container->get(\Slim\Psr7\Factory\ServerRequestFactory::class);
  },
];
