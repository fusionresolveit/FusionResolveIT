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

final class Linetype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Linetype::class;

  protected function instanciateModel(): \App\Models\Linetype
  {
    return new \App\Models\Linetype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Linetype::class);

    $linetype = new \App\Models\Linetype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($linetype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $linetype = \App\Models\Linetype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($linetype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/linetypes/' . $linetype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/linetypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Linetype::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $linetype = \App\Models\Linetype::where('id', $id)->first();
    if (is_null($linetype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($linetype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $linetype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($linetype, 'update');

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
    $linetype = \App\Models\Linetype::withTrashed()->where('id', $id)->first();
    if (is_null($linetype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($linetype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $linetype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/linetypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $linetype->delete();
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
    $linetype = \App\Models\Linetype::withTrashed()->where('id', $id)->first();
    if (is_null($linetype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($linetype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $linetype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
