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

final class Devicepcimodel extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Devicepcimodel::class;

  protected function instanciateModel(): \App\Models\Devicepcimodel
  {
    return new \App\Models\Devicepcimodel();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandardDevicemodel((object) $request->getParsedBody(), \App\Models\Devicepcimodel::class);

    $devicepcimodel = new \App\Models\Devicepcimodel();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicepcimodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicepcimodel = \App\Models\Devicepcimodel::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The pci model has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicepcimodel, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicepcimodels/' . $devicepcimodel->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicepcimodels')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandardDevicemodel((object) $request->getParsedBody(), \App\Models\Devicepcimodel::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicepcimodel = \App\Models\Devicepcimodel::where('id', $id)->first();
    if (is_null($devicepcimodel))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicepcimodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicepcimodel->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The pci model has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicepcimodel, 'update');

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
    $devicepcimodel = \App\Models\Devicepcimodel::withTrashed()->where('id', $id)->first();
    if (is_null($devicepcimodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicepcimodel->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicepcimodel->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The pci model has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/devicepcimodels')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicepcimodel->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The pci model has been soft deleted successfully');
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
    $devicepcimodel = \App\Models\Devicepcimodel::withTrashed()->where('id', $id)->first();
    if (is_null($devicepcimodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicepcimodel->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicepcimodel->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The pci model has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
