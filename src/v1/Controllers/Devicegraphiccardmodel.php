<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandardDevicemodel;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Devicegraphiccardmodel extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Devicegraphiccardmodel::class;

  protected function instanciateModel(): \App\Models\Devicegraphiccardmodel
  {
    return new \App\Models\Devicegraphiccardmodel();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandardDevicemodel((object) $request->getParsedBody(), \App\Models\Devicegraphiccardmodel::class);

    $devicegraphiccardmodel = new \App\Models\Devicegraphiccardmodel();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicegraphiccardmodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicegraphiccardmodel = \App\Models\Devicegraphiccardmodel::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The graphic card model has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicegraphiccardmodel, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicegraphiccardmodels/' . $devicegraphiccardmodel->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicegraphiccardmodels')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandardDevicemodel((object) $request->getParsedBody(), \App\Models\Devicegraphiccardmodel::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicegraphiccardmodel = \App\Models\Devicegraphiccardmodel::where('id', $id)->first();
    if (is_null($devicegraphiccardmodel))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicegraphiccardmodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicegraphiccardmodel->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The graphic card model has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicegraphiccardmodel, 'update');

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
    $devicegraphiccardmodel = \App\Models\Devicegraphiccardmodel::withTrashed()->where('id', $id)->first();
    if (is_null($devicegraphiccardmodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicegraphiccardmodel->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicegraphiccardmodel->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The graphic card model has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/devicegraphiccardmodels')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicegraphiccardmodel->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The graphic card model has been soft deleted successfully');
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
    $devicegraphiccardmodel = \App\Models\Devicegraphiccardmodel::withTrashed()->where('id', $id)->first();
    if (is_null($devicegraphiccardmodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicegraphiccardmodel->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicegraphiccardmodel->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The graphic card model has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
