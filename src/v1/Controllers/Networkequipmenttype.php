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

final class Networkequipmenttype extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Networkequipmenttype::class;

  protected function instanciateModel(): \App\Models\Networkequipmenttype
  {
    return new \App\Models\Networkequipmenttype();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Networkequipmenttype::class);

    $networkequipmenttype = new \App\Models\Networkequipmenttype();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($networkequipmenttype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $networkequipmenttype = \App\Models\Networkequipmenttype::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The networkequipment type has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($networkequipmenttype, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/networkequipmenttypes/' . $networkequipmenttype->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/networkequipmenttypes')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Networkequipmenttype::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $networkequipmenttype = \App\Models\Networkequipmenttype::where('id', $id)->first();
    if (is_null($networkequipmenttype))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($networkequipmenttype))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $networkequipmenttype->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The networkequipment type has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($networkequipmenttype, 'update');

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
    $networkequipmenttype = \App\Models\Networkequipmenttype::withTrashed()->where('id', $id)->first();
    if (is_null($networkequipmenttype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($networkequipmenttype->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $networkequipmenttype->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The networkequipment type has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/networkequipmenttypes')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $networkequipmenttype->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The networkequipment type has been soft deleted successfully');
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
    $networkequipmenttype = \App\Models\Networkequipmenttype::withTrashed()->where('id', $id)->first();
    if (is_null($networkequipmenttype))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($networkequipmenttype->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $networkequipmenttype->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The networkequipment type has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
