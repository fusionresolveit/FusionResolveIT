<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostOperatingsystemversion;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Operatingsystemversion extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Operatingsystemversion::class;

  protected function instanciateModel(): \App\Models\Operatingsystemversion
  {
    return new \App\Models\Operatingsystemversion();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostOperatingsystemversion((object) $request->getParsedBody());

    $operatingsystemversion = new \App\Models\Operatingsystemversion();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($operatingsystemversion))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $operatingsystemversion = \App\Models\Operatingsystemversion::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($operatingsystemversion, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/operatingsystemversions/' . $operatingsystemversion->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/operatingsystemversions')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostOperatingsystemversion((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $operatingsystemversion = \App\Models\Operatingsystemversion::where('id', $id)->first();
    if (is_null($operatingsystemversion))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($operatingsystemversion))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $operatingsystemversion->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($operatingsystemversion, 'update');

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
    $operatingsystemversion = \App\Models\Operatingsystemversion::withTrashed()->where('id', $id)->first();
    if (is_null($operatingsystemversion))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($operatingsystemversion->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $operatingsystemversion->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/operatingsystemversions')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $operatingsystemversion->delete();
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
    $operatingsystemversion = \App\Models\Operatingsystemversion::withTrashed()->where('id', $id)->first();
    if (is_null($operatingsystemversion))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($operatingsystemversion->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $operatingsystemversion->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
