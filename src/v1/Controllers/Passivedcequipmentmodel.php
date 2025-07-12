<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostPassivedcequipmentmodel;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Passivedcequipmentmodel extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Passivedcequipmentmodel::class;

  protected function instanciateModel(): \App\Models\Passivedcequipmentmodel
  {
    return new \App\Models\Passivedcequipmentmodel();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostPassivedcequipmentmodel((object) $request->getParsedBody());

    $passivedcequipmentmodel = new \App\Models\Passivedcequipmentmodel();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($passivedcequipmentmodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $passivedcequipmentmodel = \App\Models\Passivedcequipmentmodel::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($passivedcequipmentmodel, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/passivedcequipmentmodels/' . $passivedcequipmentmodel->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/passivedcequipmentmodels')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostPassivedcequipmentmodel((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $passivedcequipmentmodel = \App\Models\Passivedcequipmentmodel::where('id', $id)->first();
    if (is_null($passivedcequipmentmodel))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($passivedcequipmentmodel))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $passivedcequipmentmodel->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($passivedcequipmentmodel, 'update');

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
    $passivedcequipmentmodel = \App\Models\Passivedcequipmentmodel::withTrashed()->where('id', $id)->first();
    if (is_null($passivedcequipmentmodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($passivedcequipmentmodel->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $passivedcequipmentmodel->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/passivedcequipmentmodels')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $passivedcequipmentmodel->delete();
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
    $passivedcequipmentmodel = \App\Models\Passivedcequipmentmodel::withTrashed()->where('id', $id)->first();
    if (is_null($passivedcequipmentmodel))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($passivedcequipmentmodel->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $passivedcequipmentmodel->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
