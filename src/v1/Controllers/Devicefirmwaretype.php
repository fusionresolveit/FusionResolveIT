<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Devicefirmwaretype extends Common
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  protected $model = \App\Models\Devicefirmwaretype::class;

  protected function instanciateModel(): \App\Models\Devicefirmwaretype
  {
    return new \App\Models\Devicefirmwaretype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Devicefirmwaretype::class);

    $devicefirmwaretype = new \App\Models\Devicefirmwaretype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicefirmwaretype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicefirmwaretype = \App\Models\Devicefirmwaretype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The firmware type has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicefirmwaretype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicefirmwaretypes/' . $devicefirmwaretype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicefirmwaretypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Devicefirmwaretype::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicefirmwaretype = \App\Models\Devicefirmwaretype::where('id', $id)->first();
    if (is_null($devicefirmwaretype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicefirmwaretype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicefirmwaretype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The firmware type has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicefirmwaretype, 'update');

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
    $devicefirmwaretype = \App\Models\Devicefirmwaretype::withTrashed()->where('id', $id)->first();
    if (is_null($devicefirmwaretype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicefirmwaretype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicefirmwaretype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The firmware type has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/devicefirmwaretypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicefirmwaretype->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The firmware type has been soft deleted successfully');
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
    $devicefirmwaretype = \App\Models\Devicefirmwaretype::withTrashed()->where('id', $id)->first();
    if (is_null($devicefirmwaretype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicefirmwaretype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicefirmwaretype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The firmware type has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
