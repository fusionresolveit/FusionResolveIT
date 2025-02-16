<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandardDevicemodel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Devicefirmwaremodel extends Common implements \App\Interfaces\Crud
{
  protected $model = \App\Models\Devicefirmwaremodel::class;

    /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandardDevicemodel((object) $request->getParsedBody(), \App\Models\Devicefirmwaremodel::class);

    $devicefirmwaremodel = new \App\Models\Devicefirmwaremodel();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicefirmwaremodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicefirmwaremodel = \App\Models\Devicefirmwaremodel::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The firmware model has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicefirmwaremodel, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicefirmwaremodels/' . $devicefirmwaremodel->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicefirmwaremodels')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandardDevicemodel((object) $request->getParsedBody(), \App\Models\Devicefirmwaremodel::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicefirmwaremodel = \App\Models\Devicefirmwaremodel::where('id', $id)->first();
    if (is_null($devicefirmwaremodel))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicefirmwaremodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicefirmwaremodel->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The firmware model has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicefirmwaremodel, 'update');

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
    $devicefirmwaremodel = \App\Models\Devicefirmwaremodel::withTrashed()->where('id', $id)->first();
    if (is_null($devicefirmwaremodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicefirmwaremodel->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicefirmwaremodel->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The firmware model has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/devicefirmwaremodels')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicefirmwaremodel->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The devicefirmwaremodel has been soft deleted successfully');
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
    $devicefirmwaremodel = \App\Models\Devicefirmwaremodel::withTrashed()->where('id', $id)->first();
    if (is_null($devicefirmwaremodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicefirmwaremodel->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicefirmwaremodel->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The firmware model has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
