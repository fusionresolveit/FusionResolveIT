<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostDevicesimcard;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Item;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Devicesimcard extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Document;
  use History;
  use Item;

  protected $model = \App\Models\Devicesimcard::class;
  protected $rootUrl2 = '/devices/devicesimcards/';
  protected $choose = 'devicesimcards';

  protected function instanciateModel(): \App\Models\Devicesimcard
  {
    return new \App\Models\Devicesimcard();
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

    $data = new PostDevicesimcard((object) $request->getParsedBody());

    $devicesimcard = new \App\Models\Devicesimcard();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicesimcard))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicesimcard = \App\Models\Devicesimcard::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The sim card has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicesimcard, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicesimcards/' . $devicesimcard->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicesimcards')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostDevicesimcard((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicesimcard = \App\Models\Devicesimcard::where('id', $id)->first();
    if (is_null($devicesimcard))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicesimcard))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicesimcard->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The sim card has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicesimcard, 'update');

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
    $devicesimcard = \App\Models\Devicesimcard::withTrashed()->where('id', $id)->first();
    if (is_null($devicesimcard))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicesimcard->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicesimcard->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The sim card has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/devicesimcards')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicesimcard->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The sim card has been soft deleted successfully');
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
    $devicesimcard = \App\Models\Devicesimcard::withTrashed()->where('id', $id)->first();
    if (is_null($devicesimcard))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicesimcard->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicesimcard->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The sim card has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
