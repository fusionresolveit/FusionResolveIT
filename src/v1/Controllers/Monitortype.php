<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandard;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Monitortype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Monitortype::class;

  protected function instanciateModel(): \App\Models\Monitortype
  {
    return new \App\Models\Monitortype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Monitortype::class);

    $monitortype = new \App\Models\Monitortype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($monitortype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $monitortype = \App\Models\Monitortype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The monitor type has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($monitortype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/monitortypes/' . $monitortype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/monitortypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Monitortype::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $monitortype = \App\Models\Monitortype::where('id', $id)->first();
    if (is_null($monitortype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($monitortype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $monitortype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The monitor type has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($monitortype, 'update');

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
    $monitortype = \App\Models\Monitortype::withTrashed()->where('id', $id)->first();
    if (is_null($monitortype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($monitortype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $monitortype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The monitor type has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/monitortypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $monitortype->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The monitor type has been soft deleted successfully');
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
    $monitortype = \App\Models\Monitortype::withTrashed()->where('id', $id)->first();
    if (is_null($monitortype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($monitortype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $monitortype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The monitor type has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
