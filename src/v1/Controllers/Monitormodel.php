<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostMonitormodel;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Monitormodel extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Monitormodel::class;

  protected function instanciateModel(): \App\Models\Monitormodel
  {
    return new \App\Models\Monitormodel();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostMonitormodel((object) $request->getParsedBody());

    $monitormodel = new \App\Models\Monitormodel();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($monitormodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $monitormodel = \App\Models\Monitormodel::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The monitor model has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($monitormodel, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/monitormodels/' . $monitormodel->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/monitormodels')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostMonitormodel((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $monitormodel = \App\Models\Monitormodel::where('id', $id)->first();
    if (is_null($monitormodel))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($monitormodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $monitormodel->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The monitor model has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($monitormodel, 'update');

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
    $monitormodel = \App\Models\Monitormodel::withTrashed()->where('id', $id)->first();
    if (is_null($monitormodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($monitormodel->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $monitormodel->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The monitor model has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/monitormodels')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $monitormodel->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The monitor model has been soft deleted successfully');
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
    $monitormodel = \App\Models\Monitormodel::withTrashed()->where('id', $id)->first();
    if (is_null($monitormodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($monitormodel->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $monitormodel->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The monitor model has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
