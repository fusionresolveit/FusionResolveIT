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

final class Networkinterface extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Networkinterface::class;

  protected function instanciateModel(): \App\Models\Networkinterface
  {
    return new \App\Models\Networkinterface();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Networkinterface::class);

    $networkinterface = new \App\Models\Networkinterface();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($networkinterface))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $networkinterface = \App\Models\Networkinterface::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The network interface has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($networkinterface, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/networkinterfaces/' . $networkinterface->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/networkinterfaces')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Networkinterface::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $networkinterface = \App\Models\Networkinterface::where('id', $id)->first();
    if (is_null($networkinterface))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($networkinterface))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $networkinterface->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The network interface has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($networkinterface, 'update');

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
    $networkinterface = \App\Models\Networkinterface::withTrashed()->where('id', $id)->first();
    if (is_null($networkinterface))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($networkinterface->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $networkinterface->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The network interface has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/networkinterfaces')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $networkinterface->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The network interface has been soft deleted successfully');
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
    $networkinterface = \App\Models\Networkinterface::withTrashed()->where('id', $id)->first();
    if (is_null($networkinterface))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($networkinterface->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $networkinterface->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The network interface has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
