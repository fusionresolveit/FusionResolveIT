<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostPeripheralmodel;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Peripheralmodel extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Peripheralmodel::class;

  protected function instanciateModel(): \App\Models\Peripheralmodel
  {
    return new \App\Models\Peripheralmodel();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostPeripheralmodel((object) $request->getParsedBody());

    $peripheralmodel = new \App\Models\Peripheralmodel();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($peripheralmodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $peripheralmodel = \App\Models\Peripheralmodel::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The peripheral model has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($peripheralmodel, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/peripheralmodels/' . $peripheralmodel->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/peripheralmodels')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostPeripheralmodel((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $peripheralmodel = \App\Models\Peripheralmodel::where('id', $id)->first();
    if (is_null($peripheralmodel))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($peripheralmodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $peripheralmodel->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The peripheral model has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($peripheralmodel, 'update');

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
    $peripheralmodel = \App\Models\Peripheralmodel::withTrashed()->where('id', $id)->first();
    if (is_null($peripheralmodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($peripheralmodel->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $peripheralmodel->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The peripheral model has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/peripheralmodels')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $peripheralmodel->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The peripheral model has been soft deleted successfully');
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
    $peripheralmodel = \App\Models\Peripheralmodel::withTrashed()->where('id', $id)->first();
    if (is_null($peripheralmodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($peripheralmodel->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $peripheralmodel->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The peripheral model has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
