<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Devicegenerictype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Devicegenerictype::class;

  protected function instanciateModel(): \App\Models\Devicegenerictype
  {
    return new \App\Models\Devicegenerictype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Devicegenerictype::class);

    $devicegenerictype = new \App\Models\Devicegenerictype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicegenerictype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicegenerictype = \App\Models\Devicegenerictype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The device generic type has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicegenerictype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicegenerictypes/' . $devicegenerictype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicegenerictypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Devicegenerictype::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicegenerictype = \App\Models\Devicegenerictype::where('id', $id)->first();
    if (is_null($devicegenerictype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicegenerictype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicegenerictype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The device generic type has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicegenerictype, 'update');

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
    $devicegenerictype = \App\Models\Devicegenerictype::withTrashed()->where('id', $id)->first();
    if (is_null($devicegenerictype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicegenerictype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicegenerictype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The device generic type has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/devicegenerictypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicegenerictype->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The device generic type has been soft deleted successfully');
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
    $devicegenerictype = \App\Models\Devicegenerictype::withTrashed()->where('id', $id)->first();
    if (is_null($devicegenerictype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicegenerictype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicegenerictype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The device generic type has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
