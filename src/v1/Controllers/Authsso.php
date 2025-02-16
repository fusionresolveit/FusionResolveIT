<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostAuthsso;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use SocialConnect\Provider\AbstractBaseProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Authsso extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Authsso::class;

  protected function instanciateModel(): \App\Models\Authsso
  {
    return new \App\Models\Authsso();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostAuthsso((object) $request->getParsedBody());

    $authsso = new \App\Models\Authsso();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($authsso))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $authsso = \App\Models\Authsso::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The authentication SSO has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($authsso, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/authssos/' . $authsso->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/authssos')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostAuthsso((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $authsso = \App\Models\Authsso::where('id', $id)->first();
    if (is_null($authsso))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($authsso))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $authsso->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The authentication SSO has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($authsso, 'update');

    $uri = $request->getUri();
    return $response
      ->withHeader('Location', (string) $uri)
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function deleteItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $id = intval($args['id']);
    $authsso = \App\Models\Authsso::withTrashed()->where('id', $id)->first();
    if (is_null($authsso))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($authsso->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $authsso->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The authentication SSO has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/authssos')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $authsso->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The authentication SSO has been soft deleted successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function restoreItem(Request $request, Response $response, array $args): Response
  {
    $id = intval($args['id']);
    $authsso = \App\Models\Authsso::withTrashed()->where('id', $id)->first();
    if (is_null($authsso))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($authsso->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $authsso->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The authentication SSO has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  public static function initScopesForProvider(\App\Models\Authsso $item): void
  {
    $providers = \App\Models\Definitions\Authsso::getProviderArray();
    if (isset($providers[$item->provider]) && isset($providers[$item->provider]['default_scope']))
    {
      foreach ($providers[$item->provider]['default_scope'] as $scope)
      {
        $authssoscope = new \App\Models\Authssoscope();
        $authssoscope->name = $scope;
        $authssoscope->authsso_id = $item->id;
        $authssoscope->save();
      }
    }
  }

  public static function initOptionsForProvider(\App\Models\Authsso $item): void
  {
    $providers = \App\Models\Definitions\Authsso::getProviderArray();
    if (isset($providers[$item->provider]) && isset($providers[$item->provider]['default_options']))
    {
      foreach ($providers[$item->provider]['default_options'] as $key => $option)
      {
        $authssooption = new \App\Models\Authssooption();
        $authssooption->authsso_id = $item->id;
        if (is_numeric($key))
        {
          // It's only value
          $authssooption->value = $option;
        }
        else
        {
          $authssooption->key = $key;
          $authssooption->value = $option;
        }
        $authssooption->save();
      }
    }
  }

  /**
   * @param \App\Models\Authsso $item
   *
   * @return array<mixed>
   */
  protected function getInformationTop($item, Request $request): array
  {
    global $translator, $basePath;

    $uri = $request->getUri();
    return [
      [
        'key'   => 'callbackurl',
        'value' => $translator->translate('Redirect URL') . ' ' . $uri->getScheme() . '://' . $uri->getHost() .
                   $basePath . '/view/login/sso/' . $item->callbackid . '/cb',
        'link'  => null,
      ],
    ];
  }

  /**
   * @param array<mixed> $configuration
   */
  public static function getProviderInstance(string $providerName, array $configuration): AbstractBaseProvider
  {
    $httpClient = new \SocialConnect\HttpClient\Curl();

    $collectionFactory = null;
    $service =  new \SocialConnect\Auth\Service(
      new \SocialConnect\Common\HttpStack(
        $httpClient,
        new \SocialConnect\HttpClient\RequestFactory(),
        new \SocialConnect\HttpClient\StreamFactory()
      ),
      new \SocialConnect\Provider\Session\Session(),
      $configuration,
      $collectionFactory
    );
    return $service->getProvider($providerName);
  }
}
