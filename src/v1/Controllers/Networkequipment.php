<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostNetworkequipment;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Appliance;
use App\Traits\Subs\Certificate;
use App\Traits\Subs\Component;
use App\Traits\Subs\Contract;
use App\Traits\Subs\Document;
use App\Traits\Subs\Domain;
use App\Traits\Subs\Externallink;
use App\Traits\Subs\History;
use App\Traits\Subs\Infocom;
use App\Traits\Subs\Itil;
use App\Traits\Subs\Knowledgebasearticle;
use App\Traits\Subs\Note;
use App\Traits\Subs\Operatingsystem;
use App\Traits\Subs\Reservation;
use App\Traits\Subs\Software;
use App\Traits\Subs\Volume;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Networkequipment extends Common implements \App\Interfaces\Crud
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
  use Certificate;
  use Externallink;
  use Knowledgebasearticle;
  use Document;
  use Contract;
  use Software;
  use Operatingsystem;
  use Itil;
  use History;
  use Component;
  use Volume;
  use Infocom;

  protected $model = \App\Models\Networkequipment::class;
  protected $rootUrl2 = '/networkequipments/';
  protected $choose = 'networkequipments';

  protected function instanciateModel(): \App\Models\Networkequipment
  {
    return new \App\Models\Networkequipment();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostNetworkequipment((object) $request->getParsedBody());

    $networkequipment = new \App\Models\Networkequipment();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($networkequipment))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $networkequipment = \App\Models\Networkequipment::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The networkequipment has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($networkequipment, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/networkequipments/' . $networkequipment->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/networkequipments')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostNetworkequipment((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $networkequipment = \App\Models\Networkequipment::where('id', $id)->first();
    if (is_null($networkequipment))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($networkequipment))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $networkequipment->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The networkequipment has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($networkequipment, 'update');

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
    $networkequipment = \App\Models\Networkequipment::withTrashed()->where('id', $id)->first();
    if (is_null($networkequipment))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($networkequipment->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $networkequipment->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The networkequipment has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/networkequipments')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $networkequipment->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The networkequipment has been soft deleted successfully');
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
    $networkequipment = \App\Models\Networkequipment::withTrashed()->where('id', $id)->first();
    if (is_null($networkequipment))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($networkequipment->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $networkequipment->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The networkequipment has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param \App\Models\Networkequipment $item
   *
   * @return array<mixed>
   */
  protected function getInformationTop($item, Request $request): array
  {
    global $translator, $basePath;

    $tabInfos = [];

    $fusioninventoried_at = $item->getAttribute('fusioninventoried_at');
    if (!is_null($fusioninventoried_at))
    {
      $tabInfos[] = [
        'key'   => 'labelfusioninventoried',
        'value' => $translator->translate('Automatically inventoried'),
        'link'  => null,
      ];

      $tabInfos[] = [
        'key'   => 'fusioninventoried',
        'value' => $translator->translate('Last automatic inventory') . ' : ' .
                   $fusioninventoried_at->toDateTimeString(),
        'link'  => null,
      ];
    }
    return $tabInfos;
  }
}
