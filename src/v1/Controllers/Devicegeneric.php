<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostDevicegeneric;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Item;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Devicegeneric extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Document;
  use History;
  use Item;

  protected $model = \App\Models\Devicegeneric::class;
  protected $rootUrl2 = '/devices/devicegenerics/';
  protected $choose = 'devicegenerics';

  protected function instanciateModel(): \App\Models\Devicegeneric
  {
    return new \App\Models\Devicegeneric();
  }

  /**
   * @return array{
   *          'itemComputers': \App\Models\Computer,
   *          'itemNetworkequipments': \App\Models\Networkequipment,
   *          'itemPeripherals': \App\Models\Peripheral,
   *          'itemPhones': \App\Models\Phone,
   *          'itemPrinters': \App\Models\Printer,
   *         }
   */
  protected function modelsForSubItem()
  {
    return [
      'itemComputers'         => new \App\Models\Computer(),
      'itemNetworkequipments' => new \App\Models\Networkequipment(),
      'itemPeripherals'       => new \App\Models\Peripheral(),
      'itemPhones'            => new \App\Models\Phone(),
      'itemPrinters'          => new \App\Models\Printer(),

    ];
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostDevicegeneric((object) $request->getParsedBody());

    $devicegeneric = new \App\Models\Devicegeneric();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicegeneric))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicegeneric = \App\Models\Devicegeneric::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The generic device has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicegeneric, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicegenerics/' . $devicegeneric->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicegenerics')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostDevicegeneric((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicegeneric = \App\Models\Devicegeneric::where('id', $id)->first();
    if (is_null($devicegeneric))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicegeneric))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicegeneric->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The generic device has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicegeneric, 'update');

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
    $devicegeneric = \App\Models\Devicegeneric::withTrashed()->where('id', $id)->first();
    if (is_null($devicegeneric))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicegeneric->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicegeneric->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The generic device has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/devicegenerics')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicegeneric->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The generic device has been soft deleted successfully');
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
    $devicegeneric = \App\Models\Devicegeneric::withTrashed()->where('id', $id)->first();
    if (is_null($devicegeneric))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicegeneric->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicegeneric->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The generic device has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
