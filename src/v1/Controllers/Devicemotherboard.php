<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostDevicemotherboard;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Item;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Devicemotherboard extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use Document;
  use History;
  use Item;

  protected $model = \App\Models\Devicemotherboard::class;
  protected $rootUrl2 = '/devices/devicemotherboards/';
  protected $choose = 'devicemotherboards';

  protected function instanciateModel(): \App\Models\Devicemotherboard
  {
    return new \App\Models\Devicemotherboard();
  }

  /**
   * @return array{
   *          'itemComputers': \App\Models\Computer,
   *         }
   */
  protected function modelsForSubItem()
  {
    return [
      'itemComputers'         => new \App\Models\Computer(),
    ];
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostDevicemotherboard((object) $request->getParsedBody());

    $devicemotherboard = new \App\Models\Devicemotherboard();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicemotherboard))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicemotherboard = \App\Models\Devicemotherboard::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The motherboard has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicemotherboard, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicemotherboards/' . $devicemotherboard->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicemotherboards')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostDevicemotherboard((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicemotherboard = \App\Models\Devicemotherboard::where('id', $id)->first();
    if (is_null($devicemotherboard))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicemotherboard))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicemotherboard->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The motherboard has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicemotherboard, 'update');

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
    $devicemotherboard = \App\Models\Devicemotherboard::withTrashed()->where('id', $id)->first();
    if (is_null($devicemotherboard))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicemotherboard->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicemotherboard->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The motherboard has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/devicemotherboards')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicemotherboard->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The motherboard has been soft deleted successfully');
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
    $devicemotherboard = \App\Models\Devicemotherboard::withTrashed()->where('id', $id)->first();
    if (is_null($devicemotherboard))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicemotherboard->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicemotherboard->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The motherboard has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
