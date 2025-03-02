<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Plug extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Plug::class;

  protected function instanciateModel(): \App\Models\Plug
  {
    return new \App\Models\Plug();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Plug::class);

    $plug = new \App\Models\Plug();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($plug))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $plug = \App\Models\Plug::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The plug has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($plug, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/plugs/' . $plug->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/plugs')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Plug::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $plug = \App\Models\Plug::where('id', $id)->first();
    if (is_null($plug))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($plug))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $plug->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The plug has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($plug, 'update');

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
    $plug = \App\Models\Plug::withTrashed()->where('id', $id)->first();
    if (is_null($plug))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($plug->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $plug->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The plug has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/plugs')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $plug->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The plug has been soft deleted successfully');
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
    $plug = \App\Models\Plug::withTrashed()->where('id', $id)->first();
    if (is_null($plug))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($plug->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $plug->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The plug has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
