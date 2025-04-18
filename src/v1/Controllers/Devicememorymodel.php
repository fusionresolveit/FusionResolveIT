<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandardDevicemodel;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Devicememorymodel extends Common
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Devicememorymodel::class;

  protected function instanciateModel(): \App\Models\Devicememorymodel
  {
    return new \App\Models\Devicememorymodel();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandardDevicemodel((object) $request->getParsedBody(), \App\Models\Devicememorymodel::class);

    $devicememorymodel = new \App\Models\Devicememorymodel();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicememorymodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicememorymodel = \App\Models\Devicememorymodel::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The memory model has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicememorymodel, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicememorymodels/' . $devicememorymodel->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicememorymodels')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandardDevicemodel((object) $request->getParsedBody(), \App\Models\Devicememorymodel::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicememorymodel = \App\Models\Devicememorymodel::where('id', $id)->first();
    if (is_null($devicememorymodel))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicememorymodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicememorymodel->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The memory model has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicememorymodel, 'update');

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
    $devicememorymodel = \App\Models\Devicememorymodel::withTrashed()->where('id', $id)->first();
    if (is_null($devicememorymodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicememorymodel->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicememorymodel->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The memory model has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/devicememorymodels')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicememorymodel->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The memory model has been soft deleted successfully');
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
    $devicememorymodel = \App\Models\Devicememorymodel::withTrashed()->where('id', $id)->first();
    if (is_null($devicememorymodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicememorymodel->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicememorymodel->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The memory model has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
