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

final class Deviceharddrivemodel extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Deviceharddrivemodel::class;

  protected function instanciateModel(): \App\Models\Deviceharddrivemodel
  {
    return new \App\Models\Deviceharddrivemodel();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandardDevicemodel((object) $request->getParsedBody(), \App\Models\Deviceharddrivemodel::class);

    $deviceharddrivemodel = new \App\Models\Deviceharddrivemodel();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($deviceharddrivemodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $deviceharddrivemodel = \App\Models\Deviceharddrivemodel::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The hard drive model has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($deviceharddrivemodel, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/deviceharddrivemodels/' . $deviceharddrivemodel->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/deviceharddrivemodels')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandardDevicemodel((object) $request->getParsedBody(), \App\Models\Deviceharddrivemodel::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $deviceharddrivemodel = \App\Models\Deviceharddrivemodel::where('id', $id)->first();
    if (is_null($deviceharddrivemodel))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($deviceharddrivemodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $deviceharddrivemodel->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The hard drive model has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($deviceharddrivemodel, 'update');

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
    $deviceharddrivemodel = \App\Models\Deviceharddrivemodel::withTrashed()->where('id', $id)->first();
    if (is_null($deviceharddrivemodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($deviceharddrivemodel->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $deviceharddrivemodel->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The hard drive model has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/deviceharddrivemodels')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $deviceharddrivemodel->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The hard drive model has been soft deleted successfully');
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
    $deviceharddrivemodel = \App\Models\Deviceharddrivemodel::withTrashed()->where('id', $id)->first();
    if (is_null($deviceharddrivemodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($deviceharddrivemodel->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $deviceharddrivemodel->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The hard drive model has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
