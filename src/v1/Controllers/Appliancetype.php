<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandardentity;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Appliancetype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Appliancetype::class;

  protected function instanciateModel(): \App\Models\Appliancetype
  {
    return new \App\Models\Appliancetype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandardentity((object) $request->getParsedBody(), \App\Models\Appliancetype::class);

    $appliancetype = new \App\Models\Appliancetype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($appliancetype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $appliancetype = \App\Models\Appliancetype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($appliancetype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/appliancetypes/' . $appliancetype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/appliancetypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandardentity((object) $request->getParsedBody(), \App\Models\Appliancetype::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $appliancetype = \App\Models\Appliancetype::where('id', $id)->first();
    if (is_null($appliancetype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($appliancetype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $appliancetype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($appliancetype, 'update');

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
    $appliancetype = \App\Models\Appliancetype::withTrashed()->where('id', $id)->first();
    if (is_null($appliancetype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($appliancetype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $appliancetype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/appliancetypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $appliancetype->delete();
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
    $appliancetype = \App\Models\Appliancetype::withTrashed()->where('id', $id)->first();
    if (is_null($appliancetype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($appliancetype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $appliancetype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
