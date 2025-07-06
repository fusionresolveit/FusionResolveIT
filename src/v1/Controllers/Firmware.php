<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostFirmware;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Item;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Firmware extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Document;
  use History;
  use Item;

  protected $model = \App\Models\Firmware::class;
  protected $rootUrl2 = '/devices/firmware/';
  protected $choose = 'firmware';

  protected function instanciateModel(): \App\Models\Firmware
  {
    return new \App\Models\Firmware();
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

    $data = new PostFirmware((object) $request->getParsedBody());

    $firmware = new \App\Models\Firmware();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($firmware))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $firmware = \App\Models\Firmware::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The firmware has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($firmware, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/firmware/' . $firmware->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/firmware')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostFirmware((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $firmware = \App\Models\Firmware::where('id', $id)->first();
    if (is_null($firmware))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($firmware))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $firmware->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The firmware has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($firmware, 'update');

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
    $firmware = \App\Models\Firmware::withTrashed()->where('id', $id)->first();
    if (is_null($firmware))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($firmware->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $firmware->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The firmware has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/firmware')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $firmware->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The firmware has been soft deleted successfully');
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
    $firmware = \App\Models\Firmware::withTrashed()->where('id', $id)->first();
    if (is_null($firmware))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($firmware->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $firmware->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The firmware has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
