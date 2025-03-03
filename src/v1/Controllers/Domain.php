<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostDomain;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Attacheditem;
use App\Traits\Subs\Certificate;
use App\Traits\Subs\Contract;
use App\Traits\Subs\Document;
use App\Traits\Subs\Externallink;
use App\Traits\Subs\History;
use App\Traits\Subs\Infocom;
use App\Traits\Subs\Itil;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Domain extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use Certificate;
  use Externallink;
  use Document;
  use Contract;
  use Itil;
  use History;
  use Infocom;

  protected $model = \App\Models\Domain::class;
  protected $rootUrl2 = '/domains/';
  protected $choose = 'domains';

  protected function instanciateModel(): \App\Models\Domain
  {
    return new \App\Models\Domain();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostDomain((object) $request->getParsedBody());

    $domain = new \App\Models\Domain();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($domain))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $domain = \App\Models\Domain::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The domain has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($domain, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/domains/' . $domain->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/domains')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostDomain((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $domain = \App\Models\Domain::where('id', $id)->first();
    if (is_null($domain))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($domain))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $domain->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The domain has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($domain, 'update');

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
    $domain = \App\Models\Domain::withTrashed()->where('id', $id)->first();
    if (is_null($domain))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($domain->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $domain->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The domain has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/domains')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $domain->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The domain has been soft deleted successfully');
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
    $domain = \App\Models\Domain::withTrashed()->where('id', $id)->first();
    if (is_null($domain))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($domain->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $domain->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The domain has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubRecords(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Domain();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('records')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/records');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myRecords = [];
    foreach ($myItem->records as $record)
    {
      $type = '';
      $type_url = '';
      if ($record->type !== null)
      {
        $type = $record->type->name;
        $type_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/domainrecordtypes/', $record->type->id);
      }

      $myRecords[] = [
        'name'        => $record->name,
        'type'        => $type,
        'type_url'    => $type_url,
        'ttl'         => $record->ttl,
        'target'      => $record->data,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('records', $myRecords);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('ttl', $translator->translate('TTL'));
    $viewData->addTranslation('target', $translator->translatePlural('Target', 'Targets', 1));

    return $view->render($response, 'subitem/records.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubAttachedItems(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $view = Twig::fromRequest($request);

    $myItem = \App\Models\Domain::where('id', $args['id'])->first();
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
      'itemComputers',
      'itemPeripherals',
      'itemMonitors',
      'itemNetworkequipments',
      'itemPhones',
      'itemPrinters',
      'itemSoftwares',
    ];

    foreach ($itemTypes as $itemType)
    {
      $domain = \App\Models\Domain::where('id', $args['id'])->with($itemType)->first();
      if (is_null($domain))
      {
        throw new \Exception('Id not found', 404);
      }

      foreach ($domain->{$itemType} as $relationItem)
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
        $serial = $relationItem->serial;
        $otherserial = $relationItem->otherserial;

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
        if ($relationItem->state !== null)
        {
          $status = $relationItem->state->name;
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

    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('serial', $translator->translate('Serial number'));
    $viewData->addTranslation('otherserial', $translator->translate('Inventory number'));
    $viewData->addTranslation('status', $translator->translate('State'));
    $viewData->addTranslation('domain_relation', $translator->translatePlural(
      'Domain relation',
      'Domains relations',
      1
    ));
    $viewData->addTranslation('value', $translator->translate('Value'));
    $viewData->addTranslation('total', $translator->translate('Total'));

    return $view->render($response, 'subitem/attacheditems.html.twig', (array)$viewData);
  }
}
