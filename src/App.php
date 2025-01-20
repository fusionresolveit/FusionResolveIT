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
use JimTools\JwtAuth\Middleware\JwtAuthentication;
use JimTools\JwtAuth\Options;
use JimTools\JwtAuth\Rules\RequestMethodRule;
use JimTools\JwtAuth\Rules\RequestPathRule;
use JimTools\JwtAuth\Secret;
use JimTools\JwtAuth\Decoder\FirebaseDecoder;
use Spatie\ArrayToXml\ArrayToXml;

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

    $app->add(
      new JwtAuthentication(
        new Options(before: new JwtBeforeHandler(), isSecure: false),
        new FirebaseDecoder(new Secret(sodium_base642bin('TEST', SODIUM_BASE64_VARIANT_ORIGINAL), 'HS256')),
        [new RequestMethodRule(), new RequestPathRule(ignore: $ignoreList)],
      )
    );

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
      global $basePath, $phpunit;

      $view = Twig::create(__DIR__ . '/v1/Views');
      $view->addExtension(new WebpackExtension(
        __DIR__ . '/../public/assets/manifest.json',
        __DIR__ . '/../public/'
      ));

      // Manage JWT token
      if (get_class($exception) == 'JimTools\JwtAuth\Exceptions\AuthorizationException')
      {
        $GLOBALS['user_id'] = null;
        // for web, redirect to login page

        $uri = $request->getUri();

        if (!$phpunit && !str_starts_with($uri->getPath(), $basePath . '/api'))
        {
          header('Location: ' . $basePath . '/view/login');
          exit();
        }

        // for API
        $error = [
          "status"  => "error",
          "message" => "Token error"
        ];
        $response = $app->getResponseFactory()->createResponse();
        $response->getBody()->write(json_encode($error));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
      }
      elseif (get_class($exception) == 'App\v1\Controllers\Fusioninventory\FusioninventoryXmlException')
      {
        $payload = [
          'ERROR' => $exception->getMessage(),
        ];
        $response = $app->getResponseFactory()->createResponse();
        $response->getBody()->write(ArrayToXml::convert($payload, 'REPLY'));
        return $response->withStatus($exception->getCode())->withHeader('Content-Type', 'application/xml');
      }

      if ($exception->getCode() == 401)
      {
        $response = $app->getResponseFactory()->createResponse()->withStatus($exception->getCode());
        $viewData = [
          'rootpath' => $basePath,
          'message'  => $exception->getMessage(),
        ];

        return $view->render($response, 'error401.html.twig', $viewData);
      }
      elseif ($exception->getCode() == 405)
      {
        $response = $app->getResponseFactory()->createResponse()->withStatus(404);
        return $view->render($response, 'error404.html.twig', []);
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
      if (
          $response->getHeaderLine('Content-Type') == 'application/json' ||
          $response->getHeaderLine('Content-Type') == 'application/xml'
      )
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
