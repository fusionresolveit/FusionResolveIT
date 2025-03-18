<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostDevicenetworkcard;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Item;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Devicenetworkcard extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Document;
  use History;
  use Item;

  protected $model = \App\Models\Devicenetworkcard::class;
  protected $rootUrl2 = '/devices/devicenetworkcards/';
  protected $choose = 'devicenetworkcards';

  protected function instanciateModel(): \App\Models\Devicenetworkcard
  {
    return new \App\Models\Devicenetworkcard();
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

    $data = new PostDevicenetworkcard((object) $request->getParsedBody());

    $devicenetworkcard = new \App\Models\Devicenetworkcard();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicenetworkcard))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicenetworkcard = \App\Models\Devicenetworkcard::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The network card has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicenetworkcard, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicenetworkcards/' . $devicenetworkcard->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicenetworkcards')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostDevicenetworkcard((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicenetworkcard = \App\Models\Devicenetworkcard::where('id', $id)->first();
    if (is_null($devicenetworkcard))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicenetworkcard))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicenetworkcard->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The network card has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicenetworkcard, 'update');

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
    $devicenetworkcard = \App\Models\Devicenetworkcard::withTrashed()->where('id', $id)->first();
    if (is_null($devicenetworkcard))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicenetworkcard->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicenetworkcard->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The network card has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/devicenetworkcards')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicenetworkcard->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The network card has been soft deleted successfully');
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
    $devicenetworkcard = \App\Models\Devicenetworkcard::withTrashed()->where('id', $id)->first();
    if (is_null($devicenetworkcard))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicenetworkcard->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicenetworkcard->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The network card has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
