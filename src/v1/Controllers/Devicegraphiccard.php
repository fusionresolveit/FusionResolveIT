<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostDevicegraphiccard;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Item;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Devicegraphiccard extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use Document;
  use History;
  use Item;

  protected $model = \App\Models\Devicegraphiccard::class;
  protected $rootUrl2 = '/devices/devicegraphiccards/';
  protected $choose = 'devicegraphiccards';

  protected function instanciateModel(): \App\Models\Devicegraphiccard
  {
    return new \App\Models\Devicegraphiccard();
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

    $data = new PostDevicegraphiccard((object) $request->getParsedBody());

    $devicegraphiccard = new \App\Models\Devicegraphiccard();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicegraphiccard))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicegraphiccard = \App\Models\Devicegraphiccard::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The graphic card has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicegraphiccard, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicegraphiccards/' . $devicegraphiccard->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicegraphiccards')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostDevicegraphiccard((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicegraphiccard = \App\Models\Devicegraphiccard::where('id', $id)->first();
    if (is_null($devicegraphiccard))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicegraphiccard))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicegraphiccard->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The graphic card has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicegraphiccard, 'update');

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
    $devicegraphiccard = \App\Models\Devicegraphiccard::withTrashed()->where('id', $id)->first();
    if (is_null($devicegraphiccard))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicegraphiccard->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicegraphiccard->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The graphic card has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/devicegraphiccards')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicegraphiccard->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The graphic card has been soft deleted successfully');
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
    $devicegraphiccard = \App\Models\Devicegraphiccard::withTrashed()->where('id', $id)->first();
    if (is_null($devicegraphiccard))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicegraphiccard->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicegraphiccard->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The graphic card has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
