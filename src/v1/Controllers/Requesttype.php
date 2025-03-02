<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostRequesttype;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Requesttype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Requesttype::class;

  protected function instanciateModel(): \App\Models\Requesttype
  {
    return new \App\Models\Requesttype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostRequesttype((object) $request->getParsedBody());

    $requesttype = new \App\Models\Requesttype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($requesttype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $requesttype = \App\Models\Requesttype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The request type has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($requesttype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/requesttypes/' . $requesttype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/requesttypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostRequesttype((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $requesttype = \App\Models\Requesttype::where('id', $id)->first();
    if (is_null($requesttype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($requesttype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $requesttype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The request type has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($requesttype, 'update');

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
    $requesttype = \App\Models\Requesttype::withTrashed()->where('id', $id)->first();
    if (is_null($requesttype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($requesttype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $requesttype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The request type has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/requesttypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $requesttype->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The request type has been soft deleted successfully');
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
    $requesttype = \App\Models\Requesttype::withTrashed()->where('id', $id)->first();
    if (is_null($requesttype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($requesttype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $requesttype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The request type has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
