<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostPassivedcequipment;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Contract;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Infocom;
use App\Traits\Subs\Itil;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Passivedcequipment extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Document;
  use Contract;
  use Itil;
  use History;
  use Infocom;

  protected $model = \App\Models\Passivedcequipment::class;
  protected $rootUrl2 = '/passivedcequipments/';

  protected function instanciateModel(): \App\Models\Passivedcequipment
  {
    return new \App\Models\Passivedcequipment();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostPassivedcequipment((object) $request->getParsedBody());

    $passivedcequipment = new \App\Models\Passivedcequipment();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($passivedcequipment))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $passivedcequipment = \App\Models\Passivedcequipment::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The passivedcequipment has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($passivedcequipment, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/passivedcequipments/' . $passivedcequipment->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/passivedcequipments')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostPassivedcequipment((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $passivedcequipment = \App\Models\Passivedcequipment::where('id', $id)->first();
    if (is_null($passivedcequipment))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($passivedcequipment))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $passivedcequipment->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The passivedcequipment has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($passivedcequipment, 'update');

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
    $passivedcequipment = \App\Models\Passivedcequipment::withTrashed()->where('id', $id)->first();
    if (is_null($passivedcequipment))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($passivedcequipment->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $passivedcequipment->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The passivedcequipment has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/passivedcequipments')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $passivedcequipment->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The passivedcequipment has been soft deleted successfully');
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
    $passivedcequipment = \App\Models\Passivedcequipment::withTrashed()->where('id', $id)->first();
    if (is_null($passivedcequipment))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($passivedcequipment->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $passivedcequipment->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The passivedcequipment has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
