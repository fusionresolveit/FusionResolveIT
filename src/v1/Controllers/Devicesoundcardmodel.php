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

final class Devicesoundcardmodel extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Devicesoundcardmodel::class;

  protected function instanciateModel(): \App\Models\Devicesoundcardmodel
  {
    return new \App\Models\Devicesoundcardmodel();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandardDevicemodel((object) $request->getParsedBody(), \App\Models\Devicesoundcardmodel::class);

    $devicesoundcardmodel = new \App\Models\Devicesoundcardmodel();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($devicesoundcardmodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicesoundcardmodel = \App\Models\Devicesoundcardmodel::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The sound card model has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicesoundcardmodel, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/devicesoundcardmodels/' . $devicesoundcardmodel->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/devicesoundcardmodels')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandardDevicemodel((object) $request->getParsedBody(), \App\Models\Devicesoundcardmodel::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicesoundcardmodel = \App\Models\Devicesoundcardmodel::where('id', $id)->first();
    if (is_null($devicesoundcardmodel))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($devicesoundcardmodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $devicesoundcardmodel->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The sound card model has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($devicesoundcardmodel, 'update');

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
    $devicesoundcardmodel = \App\Models\Devicesoundcardmodel::withTrashed()->where('id', $id)->first();
    if (is_null($devicesoundcardmodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicesoundcardmodel->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicesoundcardmodel->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The sound card model has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/devicesoundcardmodels')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicesoundcardmodel->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The sound card model has been soft deleted successfully');
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
    $devicesoundcardmodel = \App\Models\Devicesoundcardmodel::withTrashed()->where('id', $id)->first();
    if (is_null($devicesoundcardmodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($devicesoundcardmodel->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $devicesoundcardmodel->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The sound card model has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
