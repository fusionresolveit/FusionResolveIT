<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostRack;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Contract;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Infocom;
use App\Traits\Subs\Itil;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Rack extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use Document;
  use Contract;
  use Itil;
  use History;
  use Infocom;

  protected $model = \App\Models\Rack::class;
  protected $rootUrl2 = '/racks/';

  protected function instanciateModel(): \App\Models\Rack
  {
    return new \App\Models\Rack();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostRack((object) $request->getParsedBody());

    $rack = new \App\Models\Rack();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($rack))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $rack = \App\Models\Rack::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The rack has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($rack, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/racks/' . $rack->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/racks')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostRack((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $rack = \App\Models\Rack::where('id', $id)->first();
    if (is_null($rack))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($rack))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $rack->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The rack has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($rack, 'update');

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
    $rack = \App\Models\Rack::withTrashed()->where('id', $id)->first();
    if (is_null($rack))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($rack->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $rack->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The rack has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/racks')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $rack->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The rack has been soft deleted successfully');
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
    $rack = \App\Models\Rack::withTrashed()->where('id', $id)->first();
    if (is_null($rack))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($rack->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $rack->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The rack has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
