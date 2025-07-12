<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostStandardDevicemodel;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Devicecasemodel extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Devicecasemodel::class;

  protected function instanciateModel(): \App\Models\Devicecasemodel
  {
    return new \App\Models\Devicecasemodel();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandardDevicemodel((object) $request->getParsedBody(), \App\Models\Devicecasemodel::class);

    $devicecasemodel = new \App\Models\Devicecasemodel();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicecasemodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicecasemodel = \App\Models\Devicecasemodel::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($devicecasemodel, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicecasemodels/' . $devicecasemodel->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicecasemodels')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandardDevicemodel((object) $request->getParsedBody(), \App\Models\Devicecasemodel::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicecasemodel = \App\Models\Devicecasemodel::where('id', $id)->first();
    if (is_null($devicecasemodel))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicecasemodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicecasemodel->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($devicecasemodel, 'update');

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
    $devicecasemodel = \App\Models\Devicecasemodel::withTrashed()->where('id', $id)->first();
    if (is_null($devicecasemodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicecasemodel->trashed())
    {
      if (!$this->canRightDelete())
       {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicecasemodel->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/devicecasemodels')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicecasemodel->delete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('softdeleted');
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
    $devicecasemodel = \App\Models\Devicecasemodel::withTrashed()->where('id', $id)->first();
    if (is_null($devicecasemodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicecasemodel->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicecasemodel->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
