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

final class Rackmodel extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Rackmodel::class;

  protected function instanciateModel(): \App\Models\Rackmodel
  {
    return new \App\Models\Rackmodel();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandardDevicemodel((object) $request->getParsedBody(), \App\Models\Rackmodel::class);

    $rackmodel = new \App\Models\Rackmodel();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($rackmodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $rackmodel = \App\Models\Rackmodel::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The rack model has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($rackmodel, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/rackmodels/' . $rackmodel->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/rackmodels')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandardDevicemodel((object) $request->getParsedBody(), \App\Models\Rackmodel::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $rackmodel = \App\Models\Rackmodel::where('id', $id)->first();
    if (is_null($rackmodel))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($rackmodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $rackmodel->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The rack model has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($rackmodel, 'update');

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
    $rackmodel = \App\Models\Rackmodel::withTrashed()->where('id', $id)->first();
    if (is_null($rackmodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($rackmodel->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $rackmodel->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The rack model has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/rackmodels')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $rackmodel->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The rack model has been soft deleted successfully');
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
    $rackmodel = \App\Models\Rackmodel::withTrashed()->where('id', $id)->first();
    if (is_null($rackmodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($rackmodel->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $rackmodel->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The rack model has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
