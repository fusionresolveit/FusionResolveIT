<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostPeripheral;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Appliance;
use App\Traits\Subs\Certificate;
use App\Traits\Subs\Component;
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

final class Peripheral extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use Reservation;
  use Note;
  use Domain;
  use Appliance;
  use Certificate;
  use Externallink;
  use Knowbaseitem;
  use Document;
  use Contract;
  use Software;
  use Operatingsystem;
  use Itil;
  use History;
  use Component;
  use Connection;
  use Infocom;

  protected $model = \App\Models\Peripheral::class;
  protected $rootUrl2 = '/peripherals/';
  protected $choose = 'peripherals';

  protected function instanciateModel(): \App\Models\Peripheral
  {
    return new \App\Models\Peripheral();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostPeripheral((object) $request->getParsedBody());

    $peripheral = new \App\Models\Peripheral();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($peripheral))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $peripheral = \App\Models\Peripheral::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The peripheral has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($peripheral, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/peripherals/' . $peripheral->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/peripherals')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostPeripheral((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $peripheral = \App\Models\Peripheral::where('id', $id)->first();
    if (is_null($peripheral))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($peripheral))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $peripheral->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The peripheral has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($peripheral, 'update');

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
    $peripheral = \App\Models\Peripheral::withTrashed()->where('id', $id)->first();
    if (is_null($peripheral))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($peripheral->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $peripheral->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The peripheral has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/peripherals')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $peripheral->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The peripheral has been soft deleted successfully');
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
    $peripheral = \App\Models\Peripheral::withTrashed()->where('id', $id)->first();
    if (is_null($peripheral))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($peripheral->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $peripheral->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The peripheral has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
