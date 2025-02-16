<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostDevicesensor;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Item;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Devicesensor extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use Document;
  use History;
  use Item;

  protected $model = \App\Models\Devicesensor::class;
  protected $rootUrl2 = '/devices/devicesensors/';
  protected $choose = 'devicesensors';

  protected function instanciateModel(): \App\Models\Devicesensor
  {
    return new \App\Models\Devicesensor();
  }

  /**
   * @return array{
   *          'itemComputers': \App\Models\Computer,
   *          'itemPeripherals': \App\Models\Peripheral,
   *         }
   */
  protected function modelsForSubItem()
  {
    return [
      'itemComputers'   => new \App\Models\Computer(),
      'itemPeripherals' => new \App\Models\Peripheral(),
    ];
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostDevicesensor((object) $request->getParsedBody());

    $devicesensor = new \App\Models\Devicesensor();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicesensor))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicesensor = \App\Models\Devicesensor::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The sensor has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicesensor, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicesensors/' . $devicesensor->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicesensors')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostDevicesensor((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicesensor = \App\Models\Devicesensor::where('id', $id)->first();
    if (is_null($devicesensor))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicesensor))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicesensor->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The sensor has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicesensor, 'update');

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
    $devicesensor = \App\Models\Devicesensor::withTrashed()->where('id', $id)->first();
    if (is_null($devicesensor))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicesensor->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicesensor->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The sensor has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/devicesensors')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicesensor->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The sensor has been soft deleted successfully');
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
    $devicesensor = \App\Models\Devicesensor::withTrashed()->where('id', $id)->first();
    if (is_null($devicesensor))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicesensor->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicesensor->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The sensor has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
