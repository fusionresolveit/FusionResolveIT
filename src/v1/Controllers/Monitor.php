<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostMonitor;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Appliance;
use App\Traits\Subs\Connection;
use App\Traits\Subs\Contract;
use App\Traits\Subs\Document;
use App\Traits\Subs\Domain;
use App\Traits\Subs\Externallink;
use App\Traits\Subs\History;
use App\Traits\Subs\Infocom;
use App\Traits\Subs\Itil;
use App\Traits\Subs\Knowbaseitem;
use App\Traits\Subs\Note;
use App\Traits\Subs\Operatingsystem;
use App\Traits\Subs\Reservation;
use App\Traits\Subs\Software;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Monitor extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Reservation;
  use Note;
  use Domain;
  use Appliance;
  use Externallink;
  use Knowbaseitem;
  use Document;
  use Contract;
  use Software;
  use Operatingsystem;
  use Itil;
  use History;
  use Connection;
  use Infocom;

  protected $model = \App\Models\Monitor::class;
  protected $rootUrl2 = '/monitors/';
  protected $choose = 'monitors';

  protected function instanciateModel(): \App\Models\Monitor
  {
    return new \App\Models\Monitor();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostMonitor((object) $request->getParsedBody());

    $monitor = new \App\Models\Monitor();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($monitor))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $monitor = \App\Models\Monitor::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The monitor has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($monitor, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/monitors/' . $monitor->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/monitors')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostMonitor((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $monitor = \App\Models\Monitor::where('id', $id)->first();
    if (is_null($monitor))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($monitor))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $monitor->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The monitor has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($monitor, 'update');

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
    $monitor = \App\Models\Monitor::withTrashed()->where('id', $id)->first();
    if (is_null($monitor))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($monitor->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $monitor->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The monitor has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/monitors')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $monitor->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The monitor has been soft deleted successfully');
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
    $monitor = \App\Models\Monitor::withTrashed()->where('id', $id)->first();
    if (is_null($monitor))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($monitor->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $monitor->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The monitor has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
