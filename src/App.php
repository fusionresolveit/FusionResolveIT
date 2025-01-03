<?php

declare(strict_types=1);

namespace App;

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\ErrorHandler\ErrorHandler as SymfonyErrorHandler;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Fullpipe\TwigWebpackExtension\WebpackExtension;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class App
{
  /**
   * Stores an instance of the Slim application.
   *
   * @var \Slim\App
   */
  private $app;

  public function __construct()
  {
    global $phpunit, $basePath;

    $app = AppFactory::create();

    // Create Twig
    $twig = Twig::create(__DIR__ . '/v1/Views');

    $publicPath = __DIR__;

    $twig->addExtension(new WebpackExtension(
      $publicPath . '/../public/assets/manifest.json',
      $publicPath . '/../public/'
    ));

    // Add Twig-View Middleware
    $app->add(new TwigMiddleware($twig, $app->getRouteCollector()->getRouteParser(), '', 'view'));

    $app->addRoutingMiddleware();
    $app->setBasePath($basePath);

    // See https://github.com/tuupola/slim-jwt-auth
    $container = $app->getContainer();

    $container["jwt"] = function ($container)
    {
      return new \StdClass();
    };

    $secret = sodium_base642bin('TEST', SODIUM_BASE64_VARIANT_ORIGINAL);

    $capsule = new Capsule();
    $dbConfig = include(__DIR__ . '/../phinx.php');

    $myDatabase = $dbConfig['environments'][$dbConfig['environments']['default_environment']];
    if ($phpunit)
    {
      $myDatabase = $dbConfig['environments']['tests'];
    }
    $configdb = [
      'driver'    => $myDatabase['adapter'],
      'host'      => $myDatabase['host'],
      'database'  => $myDatabase['name'],
      'username'  => $myDatabase['user'],
      'password'  => $myDatabase['pass'],
      'charset'   => $myDatabase['charset'],
      'collation' => $myDatabase['collation'],
    ];
    $capsule->addConnection($configdb);
    $capsule->setEventDispatcher(new Dispatcher(new Container()));
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    $ignoreList = [
      $basePath . "/ping",
      $basePath . "/view/login",
      $basePath . "/view/sso",
      $basePath . "/view/sso/cb",
      $basePath . "/api/v1/fusioninventory",
    ];
    if (!$phpunit)
    {
      $mailcollectors = \App\Models\Mailcollector::where('is_active', true)->where('is_oauth', true)->get();
      foreach ($mailcollectors as $collector)
      {
        $ignoreList[] = $basePath . "/view/mailcollectors/" . $collector->id . "/oauth/cb";
      }
    }

    $app->add(new \Tuupola\Middleware\JwtAuthentication([
      "ignore" => $ignoreList,
      "secure" => false,
      "secret" => $secret,
      "before" => function ($request, $arguments)
      {
        /** @var \App\Models\User|null */
        $myUser = \App\Models\User::find($arguments['decoded']['user_id']);
        // $jwtid = $myUser->getPropertyAttribute('userjwtid');
        // if (is_null($jwtid) || $jwtid != $arguments['decoded']['jti'])
        // {
        //   throw new Exception('jti changed, ask for a new token ' . $myUser['jwtid'] . ' != ' .
        //                       $arguments['decoded']['jti'], 401);
        // }
        $GLOBALS['user_id'] = $arguments['decoded']['user_id'];
        $GLOBALS['username'] = $myUser->completename;
        $GLOBALS['profile_id'] = $arguments['decoded']['profile_id'];
        $GLOBALS['entity_id'] = $arguments['decoded']['entity_id'];
        $GLOBALS['entity_treepath'] = $arguments['decoded']['entity_treepath'];
        $GLOBALS['entity_recursive'] = $arguments['decoded']['entity_recursive'];
      },
      "error" => function ($response, $arguments)
      {
        global $basePath, $phpunit;

        $GLOBALS['user_id'] = null;
        // for web, redirect to login page
        if (!$phpunit)
        {
          header('Location: ' . $basePath . '/view/login');
          exit();
        }

        // for API
        throw new \Exception($arguments["message"], 401);
      }
    ]));

    // Init session
    $app->add(
      new \Slim\Middleware\Session([
        'name' => 'gsit_session',
        'autorefresh' => true,
        'lifetime' => '1 hour',
      ])
    );

    // Define routes
    \App\Route::setRoutes($app);

    // Define Custom Error Handler
    $customErrorHandler = function (
      Request $request,
      \Throwable $exception,
      bool $displayErrorDetails,
      bool $logErrors,
      bool $logErrorDetails
    ) use ($app)
    {
      global $basePath;

      if ($exception->getCode() == 401)
      {
        $view = Twig::create(__DIR__ . '/v1/Views');
        $view->addExtension(new WebpackExtension(
          __DIR__ . '/../public/assets/manifest.json',
          __DIR__ . '/../public/'
        ));

        $response = $app->getResponseFactory()->createResponse()->withStatus($exception->getCode());
        $viewData = [
          'rootpath' => $basePath,
          'message'  => $exception->getMessage(),
        ];

        return $view->render($response, 'error401.html.twig', $viewData);
      } else {
        echo $exception->getMessage();
        echo "<br>";
        echo $exception->getFile() . " at line " . $exception->getLine();
        echo "<pre>";
        echo $exception->getTraceAsString();
        echo "</pre>";
      }
    };

    $errorMiddleware = $app->addErrorMiddleware(true, true, true);
    $errorMiddleware->setDefaultErrorHandler($customErrorHandler);

    // get php errors (warning...)
    SymfonyErrorHandler::register();

    // Middleware to replace auto path for vendor.(js|css) by real path
    $app->add(function (Request $request, RequestHandler $handler) use ($basePath)
    {
      $response = $handler->handle($request);
      if ($response->getHeaderLine('Content-Type') == 'application/json')
      {
        return $response;
      }
      $body = (string) $response->getBody();

      $body = str_replace('auto/vendor.', $basePath . '/assets/vendor.', $body);
      $response->getBody()->rewind();
      $response->getBody()->write($body);

      return $response;
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
