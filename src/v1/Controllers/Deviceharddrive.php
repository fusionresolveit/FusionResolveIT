<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostDeviceharddrive;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Item;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Deviceharddrive extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use Document;
  use History;
  use Item;

  protected $model = \App\Models\Deviceharddrive::class;
  protected $rootUrl2 = '/devices/deviceharddrives/';
  protected $choose = 'deviceharddrives';

  protected function instanciateModel(): \App\Models\Deviceharddrive
  {
    return new \App\Models\Deviceharddrive();
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

    $data = new PostDeviceharddrive((object) $request->getParsedBody());

    $deviceharddrive = new \App\Models\Deviceharddrive();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($deviceharddrive))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $deviceharddrive = \App\Models\Deviceharddrive::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The hard drive has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($deviceharddrive, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/deviceharddrives/' . $deviceharddrive->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/deviceharddrives')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostDeviceharddrive((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $deviceharddrive = \App\Models\Deviceharddrive::where('id', $id)->first();
    if (is_null($deviceharddrive))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($deviceharddrive))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $deviceharddrive->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The hard drive has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($deviceharddrive, 'update');

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
    $deviceharddrive = \App\Models\Deviceharddrive::withTrashed()->where('id', $id)->first();
    if (is_null($deviceharddrive))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($deviceharddrive->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $deviceharddrive->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The hard drive has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/deviceharddrives')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $deviceharddrive->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The hard drive has been soft deleted successfully');
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
    $deviceharddrive = \App\Models\Deviceharddrive::withTrashed()->where('id', $id)->first();
    if (is_null($deviceharddrive))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($deviceharddrive->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $deviceharddrive->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The hard drive has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
