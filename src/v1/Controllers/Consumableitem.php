<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostConsumableitem;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\Externallink;
use App\Traits\Subs\History;
use App\Traits\Subs\Infocom;
use App\Traits\Subs\Note;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Consumableitem extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Note;
  use Externallink;
  use Document;
  use History;
  use Infocom;

  protected $model = \App\Models\Consumableitem::class;
  protected $rootUrl2 = '/consumableitems/';

  protected function instanciateModel(): \App\Models\Consumableitem
  {
    return new \App\Models\Consumableitem();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostConsumableitem((object) $request->getParsedBody());

    $consumableitem = new \App\Models\Consumableitem();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($consumableitem))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $consumableitem = \App\Models\Consumableitem::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($consumableitem, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/consumableitems/' . $consumableitem->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/consumableitems')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostConsumableitem((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $consumableitem = \App\Models\Consumableitem::where('id', $id)->first();
    if (is_null($consumableitem))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($consumableitem))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $consumableitem->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($consumableitem, 'update');

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
    $consumableitem = \App\Models\Consumableitem::withTrashed()->where('id', $id)->first();
    if (is_null($consumableitem))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($consumableitem->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $consumableitem->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/consumableitems')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $consumableitem->delete();
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
    $consumableitem = \App\Models\Consumableitem::withTrashed()->where('id', $id)->first();
    if (is_null($consumableitem))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($consumableitem->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $consumableitem->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubConsumables(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Consumableitem();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('consumables')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/consumables');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myConsumables_new = [];
    $myConsumables_use = [];
    $total = 0;
    $total_new = 0;
    $total_use = 0;
    foreach ($myItem->consumables as $consumable)
    {
      $status = '';

      $date_in = $consumable->date_in;

      $url = '';

      $date_out = $consumable->date_out;
      if ($date_out !== null)
      {
        $status = npgettext('cartridge', 'Used', 'Used', 1);
        $total_use = $total_use + 1;
      }
      else
      {
        $status = npgettext('cartridge', 'New', 'New', 1);
        $total_new = $total_new + 1;
      }

      $given_to = '';
      if (($consumable->item_type != '') && ($consumable->item_id != 0))
      {
        $item3 = null;
        if ($consumable->item_type == \App\Models\User::class)
        {
          $item3 = new \App\Models\User();
        }
        if ($consumable->item_type == \App\Models\Group::class)
        {
          $item3 = new \App\Models\Group();
        }
        if (!is_null($item3))
        {
          $myItem3 = $item3->where('id', $consumable->item_id)->first();
          if ($myItem3 !== null)
          {
            $type = $item3->getTable();

            $given_to = $myItem3->name;

            $url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $myItem3->id);
          }
        }
      }

      $total = $total + 1;

      if ($consumable->date_out == null)
      {
        $myConsumables_new[] = [
          'status'       => $status,
          'date_in'      => $date_in,
        ];
      }
      else
      {
        $myConsumables_use[] = [
          'status'       => $status,
          'url'          => $url,
          'date_in'      => $date_in,
          'date_out'     => $date_out,
          'given_to'     => $given_to,
        ];
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('consumables_new', $myConsumables_new);
    $viewData->addData('consumables_use', $myConsumables_use);
    $viewData->addData('total', $total);
    $viewData->addData('total_new', $total_new);
    $viewData->addData('total_use', $total_use);

    $viewData->addTranslation('status', pgettext('inventory device', 'State'));
    $viewData->addTranslation('date_in', pgettext('global', 'Add date'));
    $viewData->addTranslation('date_out', pgettext('inventory device', 'Use date'));
    $viewData->addTranslation('given_to', pgettext('consumable', 'Given to'));
    $viewData->addTranslation('no_consumable', pgettext('consumable', 'No consumable'));
    $viewData->addTranslation('consumables_use', pgettext('consumable', 'Used consumables'));
    $viewData->addTranslation('total', pgettext('global', 'Total'));
    $viewData->addTranslation(
      'total_new',
      npgettext('cartridge', 'New', 'New', 1)
    );
    $viewData->addTranslation(
      'total_use',
      npgettext('cartridge', 'Used', 'Used', 1)
    );

    return $view->render($response, 'subitem/consumables.html.twig', (array)$viewData);
  }
}
