<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Devicesensortype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Devicesensortype::class;

  protected function instanciateModel(): \App\Models\Devicesensortype
  {
    return new \App\Models\Devicesensortype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Devicesensortype::class);

    $devicesensortype = new \App\Models\Devicesensortype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicesensortype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicesensortype = \App\Models\Devicesensortype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($devicesensortype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicesensortypes/' . $devicesensortype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicesensortypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Devicesensortype::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicesensortype = \App\Models\Devicesensortype::where('id', $id)->first();
    if (is_null($devicesensortype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicesensortype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicesensortype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($devicesensortype, 'update');

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
    $devicesensortype = \App\Models\Devicesensortype::withTrashed()->where('id', $id)->first();
    if (is_null($devicesensortype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicesensortype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicesensortype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/devicesensortypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicesensortype->delete();
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
    $devicesensortype = \App\Models\Devicesensortype::withTrashed()->where('id', $id)->first();
    if (is_null($devicesensortype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicesensortype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicesensortype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
