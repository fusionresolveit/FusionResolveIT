<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Manufacturer extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Manufacturer::class;

  protected function instanciateModel(): \App\Models\Manufacturer
  {
    return new \App\Models\Manufacturer();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Manufacturer::class);

    $manufacturer = new \App\Models\Manufacturer();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($manufacturer))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $manufacturer = \App\Models\Manufacturer::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The manufacturer has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($manufacturer, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/manufacturers/' . $manufacturer->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/manufacturers')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Manufacturer::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $manufacturer = \App\Models\Manufacturer::where('id', $id)->first();
    if (is_null($manufacturer))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($manufacturer))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $manufacturer->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The manufacturer has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($manufacturer, 'update');

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
    $manufacturer = \App\Models\Manufacturer::withTrashed()->where('id', $id)->first();
    if (is_null($manufacturer))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($manufacturer->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $manufacturer->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The manufacturer has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/manufacturers')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $manufacturer->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The manufacturer has been soft deleted successfully');
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
    $manufacturer = \App\Models\Manufacturer::withTrashed()->where('id', $id)->first();
    if (is_null($manufacturer))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($manufacturer->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $manufacturer->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The manufacturer has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
