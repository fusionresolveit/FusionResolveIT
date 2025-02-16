<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostNetpoint;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Netpoint extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Netpoint::class;

  protected function instanciateModel(): \App\Models\Netpoint
  {
    return new \App\Models\Netpoint();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostNetpoint((object) $request->getParsedBody());

    $netpoint = new \App\Models\Netpoint();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($netpoint))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $netpoint = \App\Models\Netpoint::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The netpoint has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($netpoint, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/netpoints/' . $netpoint->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/netpoints')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostNetpoint((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $netpoint = \App\Models\Netpoint::where('id', $id)->first();
    if (is_null($netpoint))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($netpoint))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $netpoint->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The netpoint has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($netpoint, 'update');

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
    $netpoint = \App\Models\Netpoint::withTrashed()->where('id', $id)->first();
    if (is_null($netpoint))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($netpoint->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $netpoint->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The netpoint has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/netpoints')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $netpoint->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The netpoint has been soft deleted successfully');
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
    $netpoint = \App\Models\Netpoint::withTrashed()->where('id', $id)->first();
    if (is_null($netpoint))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($netpoint->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $netpoint->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The netpoint has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
