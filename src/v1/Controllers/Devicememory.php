<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostDevicememory;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Item;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Devicememory extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Document;
  use History;
  use Item;

  protected $model = \App\Models\Devicememory::class;
  protected $rootUrl2 = '/devices/devicememories/';
  protected $choose = 'devicememories';

  protected function instanciateModel(): \App\Models\Devicememory
  {
    return new \App\Models\Devicememory();
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

    $data = new PostDevicememory((object) $request->getParsedBody());

    $devicememory = new \App\Models\Devicememory();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicememory))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicememory = \App\Models\Devicememory::create($data->exportToArray());

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
      ->withHeader('Location', $basePath . '/view/devicememories')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostDevicememory((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicememory = \App\Models\Devicememory::where('id', $id)->first();
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
    $devicememory = \App\Models\Devicememory::withTrashed()->where('id', $id)->first();
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
        ->withHeader('Location', $basePath . '/view/devicememories')
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
    $devicememory = \App\Models\Devicememory::withTrashed()->where('id', $id)->first();
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
