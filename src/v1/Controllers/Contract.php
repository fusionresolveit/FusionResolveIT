<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostContract;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Attacheditem;
use App\Traits\Subs\Cost;
use App\Traits\Subs\Document;
use App\Traits\Subs\Externallink;
use App\Traits\Subs\History;
use App\Traits\Subs\Knowledgebasearticle;
use App\Traits\Subs\Note;
use App\Traits\Subs\Supplier;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Contract extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Note;
  use Externallink;
  use Knowledgebasearticle;
  use Document;
  use Supplier;
  use History;
  use Cost;

  protected $model = \App\Models\Contract::class;
  protected $rootUrl2 = '/contracts/';
  protected $choose = 'contracts';

  protected function instanciateModel(): \App\Models\Contract
  {
    return new \App\Models\Contract();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostContract((object) $request->getParsedBody());

    $contract = new \App\Models\Contract();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($contract))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $contract = \App\Models\Contract::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($contract, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/contracts/' . $contract->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/contracts')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostContract((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $contract = \App\Models\Contract::where('id', $id)->first();
    if (is_null($contract))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($contract))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $contract->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($contract, 'update');

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
    $contract = \App\Models\Contract::withTrashed()->where('id', $id)->first();
    if (is_null($contract))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($contract->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $contract->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/contracts')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $contract->delete();
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
    $contract = \App\Models\Contract::withTrashed()->where('id', $id)->first();
    if (is_null($contract))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($contract->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $contract->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubAttachedItems(Request $request, Response $response, array $args): Response
  {
    $view = Twig::fromRequest($request);

    $myItem = \App\Models\Contract::where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/attacheditems');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myAttachedItems = [];
    $nb_total = 0;

    $itemTypes = [
      'itemAppliances',
      'itemCertificates',
      'itemClusters',
      'itemComputers',
      'itemDcrooms',
      'itemDomains',
      'itemEnclosures',
      'itemLines',
      'itemMonitors',
      'itemNetworkequipments',
      'itemPassivedcequipments',
      'itemPdus',
      'itemPeripherals',
      'itemPhones',
      'itemPrinters',
      'itemProjects',
      'itemRacks',
      'itemSoftwares',
      'itemSoftwarelicenses',
      'itemSuppliers',
    ];


    foreach ($itemTypes as $itemType)
    {
      $contract = \App\Models\Contract::where('id', $args['id'])->with($itemType)->first();
      if (is_null($contract))
      {
        throw new \Exception('Id not found', 404);
      }

      foreach ($contract->{$itemType} as $relationItem)
      {
        $type_fr = $relationItem->getTitle();
        $type = $relationItem->getTable();

        $entity = '';
        $entity_url = '';
        if ($relationItem->entity !== null)
        {
          $entity = $relationItem->entity->completename;
          $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $relationItem->entity->id);
        }

        $nom = $relationItem->name;
        $nom_url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $relationItem->id);
        $serial = $relationItem->getAttribute('serial');
        $otherserial = $relationItem->getAttribute('otherserial');

        $first = false;
        if (array_key_exists($type, $myAttachedItems) !== true)
        {
          $myAttachedItems[$type] = [
            'name'  => $type_fr,
            'nb'    => 0,
            'items' => [],
          ];

          $first = true;
        }

        $status = '';
        if ($relationItem->getAttribute('state') !== null)
        {
          $status = $relationItem->getAttribute('state')->name;
        }

        $domain_relation = '';
        $domain_relation_url = '';

        $value = $this->showCosts($relationItem->getRelationValue('pivot')->value);

        $myAttachedItems[$type]['items'][$relationItem->id] = [
          'first'                 => $first,
          'entity'                => $entity,
          'entity_url'            => $entity_url,
          'nom'                   => $nom,
          'nom_url'               => $nom_url,
          'serial'                => $serial,
          'otherserial'           => $otherserial,
          'status'                => $status,
          'domain_relation'       => $domain_relation,
          'domain_relation_url'   => $domain_relation_url,
          'value'                 => $value,
        ];
        $myAttachedItems[$type]['nb'] = count($myAttachedItems[$type]['items']);
      }
    }

    // tri par ordre alpha
    array_multisort(array_column($myAttachedItems, 'name'), SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $myAttachedItems);

    foreach (array_keys($myAttachedItems) as $type_item)
    {
      $nb_total = $nb_total + $myAttachedItems[$type_item]['nb'];

      if (stristr($type_item, 'consumable'))
      {
        $myAttachedItems[$type_item]['name'] = $myAttachedItems[$type_item]['name'] . ' (' . $type_item . ')';
      }
      if (stristr($type_item, 'cartridge'))
      {
        $myAttachedItems[$type_item]['name'] = $myAttachedItems[$type_item]['name'] . ' (' . $type_item . ')';
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($myItem->getRelatedPages($rootUrl));

    $viewData->addData('fields', $myItem->getFormData($myItem));
    $viewData->addData('attacheditems', $myAttachedItems);
    $viewData->addData('show', $this->choose);
    $viewData->addData('nb_total', $nb_total);

    $viewData->addTranslation('type', npgettext('global', 'Type', 'Types', 1));
    $viewData->addTranslation('entity', npgettext('global', 'Entity', 'Entities', 1));
    $viewData->addTranslation('name', pgettext('global', 'Name'));
    $viewData->addTranslation('serial', pgettext('inventory device', 'Serial number'));
    $viewData->addTranslation('otherserial', pgettext('inventory device', 'Inventory number'));
    $viewData->addTranslation('status', pgettext('inventory device', 'Status'));
    $viewData->addTranslation(
      'domain_relation',
      npgettext('global', 'Domain relation', 'Domains relations', 1)
    );
    $viewData->addTranslation('value', pgettext('contract', 'Value'));
    $viewData->addTranslation('total', pgettext('global', 'Total'));

    return $view->render($response, 'subitem/attacheditems.html.twig', (array)$viewData);
  }
}
