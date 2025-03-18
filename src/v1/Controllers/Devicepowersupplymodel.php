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

final class Devicepowersupplymodel extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Devicepowersupplymodel::class;

  protected function instanciateModel(): \App\Models\Devicepowersupplymodel
  {
    return new \App\Models\Devicepowersupplymodel();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandardDevicemodel((object) $request->getParsedBody(), \App\Models\Devicepowersupplymodel::class);

    $devicepowersupplymodel = new \App\Models\Devicepowersupplymodel();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicepowersupplymodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicepowersupplymodel = \App\Models\Devicepowersupplymodel::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The powersupply model has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicepowersupplymodel, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicepowersupplymodels/' . $devicepowersupplymodel->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicepowersupplymodels')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandardDevicemodel((object) $request->getParsedBody(), \App\Models\Devicepowersupplymodel::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicepowersupplymodel = \App\Models\Devicepowersupplymodel::where('id', $id)->first();
    if (is_null($devicepowersupplymodel))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicepowersupplymodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicepowersupplymodel->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The powersupply model has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicepowersupplymodel, 'update');

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
    $devicepowersupplymodel = \App\Models\Devicepowersupplymodel::withTrashed()->where('id', $id)->first();
    if (is_null($devicepowersupplymodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicepowersupplymodel->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicepowersupplymodel->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The powersupply model has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/devicepowersupplymodels')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicepowersupplymodel->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The powersupply model has been soft deleted successfully');
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
    $devicepowersupplymodel = \App\Models\Devicepowersupplymodel::withTrashed()->where('id', $id)->first();
    if (is_null($devicepowersupplymodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicepowersupplymodel->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicepowersupplymodel->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The powersupply model has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
