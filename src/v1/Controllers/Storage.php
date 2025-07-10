<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStorage;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Item;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Storage extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Document;
  use History;
  use Item;

  protected $model = \App\Models\Storage::class;
  protected $rootUrl2 = '/devices/storages/';
  protected $choose = 'storages';

  protected function instanciateModel(): \App\Models\Storage
  {
    return new \App\Models\Storage();
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

    $data = new PostStorage((object) $request->getParsedBody());

    $storage = new \App\Models\Storage();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($storage))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $storage = \App\Models\Storage::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The storage has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($storage, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devices/storages/' . $storage->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devices/storages')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStorage((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $storage = \App\Models\Storage::where('id', $id)->first();
    if (is_null($storage))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($storage))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $storage->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The storage has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($storage, 'update');

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
    $storage = \App\Models\Storage::withTrashed()->where('id', $id)->first();
    if (is_null($storage))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($storage->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $storage->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The storage has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/devices/storages')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $storage->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The storage has been soft deleted successfully');
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
    $storage = \App\Models\Storage::withTrashed()->where('id', $id)->first();
    if (is_null($storage))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($storage->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $storage->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The storage has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param \App\Models\Storage $item
   *
   * @return array<mixed>
   */
  protected function getInformationTop($item, Request $request): array
  {
    global $translator, $basePath;

    $tabInfos = [];

    $fusioninventoried_at = $item->getAttribute('fusioninventoried_at');
    if (!is_null($fusioninventoried_at))
    {
      $tabInfos[] = [
        'key'   => 'labelfusioninventoried',
        'value' => $translator->translate('Automatically inventoried'),
        'link'  => null,
      ];

      $tabInfos[] = [
        'key'   => 'fusioninventoried',
        'value' => $translator->translate('Last automatic inventory') . ' : ' .
                   $fusioninventoried_at->toDateTimeString(),
        'link'  => null,
      ];
    }
    return $tabInfos;
  }
}
