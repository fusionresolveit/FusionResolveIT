<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostDevicesoundcard;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Item;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Devicesoundcard extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Document;
  use History;
  use Item;

  protected $model = \App\Models\Devicesoundcard::class;
  protected $rootUrl2 = '/devices/devicesoundcards/';
  protected $choose = 'devicesoundcards';

  protected function instanciateModel(): \App\Models\Devicesoundcard
  {
    return new \App\Models\Devicesoundcard();
  }

  /**
   * @return array{
   *          'itemComputers': \App\Models\Computer,
   *         }
   */
  protected function modelsForSubItem()
  {
    return [
      'itemComputers' => new \App\Models\Computer(),
    ];
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostDevicesoundcard((object) $request->getParsedBody());

    $devicesoundcard = new \App\Models\Devicesoundcard();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicesoundcard))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicesoundcard = \App\Models\Devicesoundcard::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The sound card has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicesoundcard, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicesoundcards/' . $devicesoundcard->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicesoundcards')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostDevicesoundcard((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicesoundcard = \App\Models\Devicesoundcard::where('id', $id)->first();
    if (is_null($devicesoundcard))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicesoundcard))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicesoundcard->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The sound card has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicesoundcard, 'update');

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
    $devicesoundcard = \App\Models\Devicesoundcard::withTrashed()->where('id', $id)->first();
    if (is_null($devicesoundcard))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicesoundcard->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicesoundcard->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The sound card has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/devicesoundcards')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicesoundcard->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The sound card has been soft deleted successfully');
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
    $devicesoundcard = \App\Models\Devicesoundcard::withTrashed()->where('id', $id)->first();
    if (is_null($devicesoundcard))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicesoundcard->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicesoundcard->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The sound card has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
