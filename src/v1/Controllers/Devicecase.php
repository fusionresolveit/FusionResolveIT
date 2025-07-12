<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostDevicecase;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Item;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Devicecase extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Document;
  use History;
  use Item;

  protected $model = \App\Models\Devicecase::class;
  protected $rootUrl2 = '/devices/devicecases/';
  protected $choose = 'devicecases';

  protected function instanciateModel(): \App\Models\Devicecase
  {
    return new \App\Models\Devicecase();
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

    $data = new PostDevicecase((object) $request->getParsedBody());

    $devicecase = new \App\Models\Devicecase();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicecase))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicecase = \App\Models\Devicecase::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($devicecase, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicecases/' . $devicecase->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicecases')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostDevicecase((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicecase = \App\Models\Devicecase::where('id', $id)->first();
    if (is_null($devicecase))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicecase))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicecase->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($devicecase, 'update');

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
    $devicecase = \App\Models\Devicecase::withTrashed()->where('id', $id)->first();
    if (is_null($devicecase))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicecase->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicecase->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/devicecases')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicecase->delete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('softdeleted');
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
    $devicecase = \App\Models\Devicecase::withTrashed()->where('id', $id)->first();
    if (is_null($devicecase))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicecase->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicecase->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
