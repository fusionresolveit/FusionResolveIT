<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostDevicepowersupply;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Item;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Devicepowersupply extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Document;
  use History;
  use Item;

  protected $model = \App\Models\Devicepowersupply::class;
  protected $rootUrl2 = '/devices/devicepowersupplies/';
  protected $choose = 'devicepowersupplies';

  protected function instanciateModel(): \App\Models\Devicepowersupply
  {
    return new \App\Models\Devicepowersupply();
  }

  /**
   * @return array{
   *          'itemComputers': \App\Models\Computer,
   *          'itemNetworkequipments': \App\Models\Networkequipment,
   *          'itemEnclosures': \App\Models\Enclosure
   *         }
   */
  protected function modelsForSubItem()
  {
    return [
      'itemComputers'         => new \App\Models\Computer(),
      'itemNetworkequipments' => new \App\Models\Networkequipment(),
      'itemEnclosures'         => new \App\Models\Enclosure(),
    ];
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostDevicepowersupply((object) $request->getParsedBody());

    $devicepowersupply = new \App\Models\Devicepowersupply();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicepowersupply))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicepowersupply = \App\Models\Devicepowersupply::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The powersupply has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicepowersupply, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicepowersupplies/' . $devicepowersupply->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicepowersupplies')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostDevicepowersupply((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicepowersupply = \App\Models\Devicepowersupply::where('id', $id)->first();
    if (is_null($devicepowersupply))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicepowersupply))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicepowersupply->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The powersupply has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicepowersupply, 'update');

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
    $devicepowersupply = \App\Models\Devicepowersupply::withTrashed()->where('id', $id)->first();
    if (is_null($devicepowersupply))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicepowersupply->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicepowersupply->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The powersupply has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/devicepowersupplies')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicepowersupply->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The powersupply has been soft deleted successfully');
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
    $devicepowersupply = \App\Models\Devicepowersupply::withTrashed()->where('id', $id)->first();
    if (is_null($devicepowersupply))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicepowersupply->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicepowersupply->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The powersupply has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
