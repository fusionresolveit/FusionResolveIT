<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostNetworkequipmentmodel;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Networkequipmentmodel extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Networkequipmentmodel::class;

  protected function instanciateModel(): \App\Models\Networkequipmentmodel
  {
    return new \App\Models\Networkequipmentmodel();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostNetworkequipmentmodel((object) $request->getParsedBody());

    $networkequipmentmodel = new \App\Models\Networkequipmentmodel();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($networkequipmentmodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $networkequipmentmodel = \App\Models\Networkequipmentmodel::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The networkequipment model has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($networkequipmentmodel, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/networkequipmentmodels/' . $networkequipmentmodel->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/networkequipmentmodels')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostNetworkequipmentmodel((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $networkequipmentmodel = \App\Models\Networkequipmentmodel::where('id', $id)->first();
    if (is_null($networkequipmentmodel))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($networkequipmentmodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $networkequipmentmodel->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The networkequipment model has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($networkequipmentmodel, 'update');

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
    $networkequipmentmodel = \App\Models\Networkequipmentmodel::withTrashed()->where('id', $id)->first();
    if (is_null($networkequipmentmodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($networkequipmentmodel->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $networkequipmentmodel->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The networkequipment model has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/networkequipmentmodels')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $networkequipmentmodel->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The networkequipment model has been soft deleted successfully');
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
    $networkequipmentmodel = \App\Models\Networkequipmentmodel::withTrashed()->where('id', $id)->first();
    if (is_null($networkequipmentmodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($networkequipmentmodel->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $networkequipmentmodel->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The networkequipment model has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
