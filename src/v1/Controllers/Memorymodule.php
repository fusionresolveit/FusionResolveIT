<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostMemorymodule;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Memorymodule extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Document;
  use History;

  protected $model = \App\Models\Memorymodule::class;
  protected $rootUrl2 = '/devices/memorymodules/';
  protected $choose = 'memorymodules';

  protected function instanciateModel(): \App\Models\Memorymodule
  {
    return new \App\Models\Memorymodule();
  }

  /**
   * @return array{
   *          'itemComputers': \App\Models\Computer,
   *          'itemNetworkequipments': \App\Models\Networkequipment,
   *          'itemPeripherals': \App\Models\Peripheral,
   *          'itemPrinters': \App\Models\Printer,
   *         }
   */
  protected function modelsForSubItem()
  {
    return [
      'itemComputers'         => new \App\Models\Computer(),
      'itemNetworkequipments' => new \App\Models\Networkequipment(),
      'itemPeripherals'       => new \App\Models\Peripheral(),
      'itemPrinters'          => new \App\Models\Printer(),
    ];
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostMemorymodule((object) $request->getParsedBody());

    $devicememory = new \App\Models\Memorymodule();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicememory))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicememory = \App\Models\Memorymodule::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The memory has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicememory, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicememorys/' . $devicememory->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/memorymodules')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostMemorymodule((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicememory = \App\Models\Memorymodule::where('id', $id)->first();
    if (is_null($devicememory))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicememory))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicememory->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The memory has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicememory, 'update');

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
    $devicememory = \App\Models\Memorymodule::withTrashed()->where('id', $id)->first();
    if (is_null($devicememory))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicememory->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicememory->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The memory has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/memorymodules')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicememory->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The memory has been soft deleted successfully');
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
    $devicememory = \App\Models\Memorymodule::withTrashed()->where('id', $id)->first();
    if (is_null($devicememory))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicememory->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicememory->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The memory has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
