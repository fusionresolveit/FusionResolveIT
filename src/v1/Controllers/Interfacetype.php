<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Interfacetype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Interfacetype::class;

  protected function instanciateModel(): \App\Models\Interfacetype
  {
    return new \App\Models\Interfacetype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Interfacetype::class);

    $interfacetype = new \App\Models\Interfacetype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($interfacetype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $interfacetype = \App\Models\Interfacetype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The interface type has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($interfacetype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/interfacetypes/' . $interfacetype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/interfacetypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Interfacetype::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $interfacetype = \App\Models\Interfacetype::where('id', $id)->first();
    if (is_null($interfacetype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($interfacetype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $interfacetype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The interface type has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($interfacetype, 'update');

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
    $interfacetype = \App\Models\Interfacetype::withTrashed()->where('id', $id)->first();
    if (is_null($interfacetype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($interfacetype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $interfacetype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The interface type has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/interfacetypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $interfacetype->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The interface type has been soft deleted successfully');
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
    $interfacetype = \App\Models\Interfacetype::withTrashed()->where('id', $id)->first();
    if (is_null($interfacetype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($interfacetype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $interfacetype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The interface type has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
