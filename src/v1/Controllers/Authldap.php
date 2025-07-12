<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostAuthldap;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use LdapRecord\Container;
use LdapRecord\Models\Entry;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Authldap extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  protected $model = \App\Models\Authldap::class;

  protected function instanciateModel(): \App\Models\Authldap
  {
    return new \App\Models\Authldap();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostAuthldap((object) $request->getParsedBody());

    $authldap = new \App\Models\Authldap();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($authldap))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $authldap = \App\Models\Authldap::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($authldap, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/authldaps/' . $authldap->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/authldaps')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostAuthldap((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $authldap = \App\Models\Authldap::where('id', $id)->first();
    if (is_null($authldap))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($authldap))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $authldap->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($authldap, 'update');

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
    $authldap = \App\Models\Authldap::withTrashed()->where('id', $id)->first();
    if (is_null($authldap))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($authldap->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $authldap->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/authldaps')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $authldap->delete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('softdeleted');
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
    $authldap = \App\Models\Authldap::withTrashed()->where('id', $id)->first();
    if (is_null($authldap))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($authldap->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $authldap->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  public static function importUsers(\App\Models\Authldap $ldap, string $login): mixed
  {
    $connection = new \LdapRecord\Connection([
      'hosts'     => [$ldap->host],
      'port'      => (int) $ldap->port,
      'base_dn'   => $ldap->basedn,
      'username'  => $ldap->rootdn,
      'password'  => $ldap->rootdn_passwd,
      'timeout'   => 1,
    ]);

    try
    {
      $connection->connect();
    }
    catch (\Throwable $e)
    {
      return false;
    }

    // Add the connection into the container:
    Container::addConnection($connection);

    // Get all entries:
    // $entries = Entry::find('cn=John Doe,dc=local,dc=com');
    $entries = Entry::where('cn', $login)->get();
    if (count($entries) > 0)
    {
      return current($entries)[0]->getDn();
    }
    return false;
  }

  public static function tryAuth(int $authldapId, string $userdn, string $passowrd): bool
  {
    /** @var \App\Models\Authldap|null */
    $authldap = \App\Models\Authldap::where('id', $authldapId)->first();
    if (is_null($authldap) or !$authldap->is_active)
    {
      return false;
    }

    $connection = new \LdapRecord\Connection([
      'hosts'     => [$authldap->host],
      'port'      => (int) $authldap->port,
      'timeout'   => 1,
    ]);

    if ($connection->auth()->attempt($userdn, $passowrd, $stayAuthenticated = true))
    {
      echo 'FOUND';
      return true;
    }
    return false;
  }
}
