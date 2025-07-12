<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostAppliance;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Certificate;
use App\Traits\Subs\Contract;
use App\Traits\Subs\Document;
use App\Traits\Subs\Domain;
use App\Traits\Subs\Externallink;
use App\Traits\Subs\History;
use App\Traits\Subs\Infocom;
use App\Traits\Subs\Item;
use App\Traits\Subs\Itil;
use App\Traits\Subs\Knowledgebasearticle;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Appliance extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Domain;
  use Certificate;
  use Externallink;
  use Knowledgebasearticle;
  use Document;
  use Contract;
  use Itil;
  use History;
  use Infocom;
  use Item;

  protected $model = \App\Models\Appliance::class;
  protected $rootUrl2 = '/appliances/';
  protected $choose = 'appliances';

  protected function instanciateModel(): \App\Models\Appliance
  {
    return new \App\Models\Appliance();
  }

  /**
   * @return array{
   *          'itemComputers': \App\Models\Computer,
   *          'itemMonitors': \App\Models\Monitor,
   *          'itemNetworkequipments': \App\Models\Networkequipment,
   *          'itemPeripherals': \App\Models\Peripheral,
   *          'itemPhones': \App\Models\Phone,
   *          'itemPrinters': \App\Models\Printer,
   *          'itemSoftwares': \App\Models\Software,
   *          'itemClusters': \App\Models\Cluster
   *         }
   */
  protected function modelsForSubItem()
  {
    return [
      'itemComputers'         => new \App\Models\Computer(),
      'itemMonitors'          => new \App\Models\Monitor(),
      'itemNetworkequipments' => new \App\Models\Networkequipment(),
      'itemPeripherals'       => new \App\Models\Peripheral(),
      'itemPhones'            => new \App\Models\Phone(),
      'itemPrinters'          => new \App\Models\Printer(),
      'itemSoftwares'         => new \App\Models\Software(),
      'itemClusters'          => new \App\Models\Cluster(),
    ];
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostAppliance((object) $request->getParsedBody());

    $appliance = new \App\Models\Appliance();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($appliance))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $appliance = \App\Models\Appliance::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($appliance, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/appliances/' . $appliance->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/appliances')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostAppliance((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $appliance = \App\Models\Appliance::where('id', $id)->first();
    if (is_null($appliance))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($appliance))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $appliance->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($appliance, 'update');

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
    $appliance = \App\Models\Appliance::withTrashed()->where('id', $id)->first();
    if (is_null($appliance))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($appliance->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $appliance->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/appliances')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $appliance->delete();
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
    $appliance = \App\Models\Appliance::withTrashed()->where('id', $id)->first();
    if (is_null($appliance))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($appliance->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $appliance->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
