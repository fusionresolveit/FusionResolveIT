<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostBudget;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\Externallink;
use App\Traits\Subs\History;
use App\Traits\Subs\Knowledgebasearticle;
use App\Traits\Subs\Note;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Budget extends Common implements \App\Interfaces\Crud
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
  use History;

  protected $model = \App\Models\Budget::class;
  protected $rootUrl2 = '/budgets/';
  protected $choose = 'budgets';

  protected function instanciateModel(): \App\Models\Budget
  {
    return new \App\Models\Budget();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostBudget((object) $request->getParsedBody());

    $budget = new \App\Models\Budget();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($budget))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $budget = \App\Models\Budget::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The budget has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($budget, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/budgets/' . $budget->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/budgets')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostBudget((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $budget = \App\Models\Budget::where('id', $id)->first();
    if (is_null($budget))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($budget))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $budget->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The budget has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($budget, 'update');

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
    $budget = \App\Models\Budget::withTrashed()->where('id', $id)->first();
    if (is_null($budget))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($budget->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $budget->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The budget has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/budgets')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $budget->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The budget has been soft deleted successfully');
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
    $budget = \App\Models\Budget::withTrashed()->where('id', $id)->first();
    if (is_null($budget))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($budget->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $budget->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The budget has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubBudgetMain(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Budget();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $infocoms = \App\Models\Infocom::where('budget_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/budgetmain');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myBudgetMain = [];
    $myBudgetMainType = [];
    $total_spent = 0;
    $total_remaining = 0;
    $total_budget = $myItem->value;
    foreach ($infocoms as $infocom)
    {
      if (
          $infocom->item_type !== \App\Models\Appliance::class &&
          $infocom->item_type !== \App\Models\Cartridgeitem::class &&
          $infocom->item_type !== \App\Models\Certificate::class &&
          $infocom->item_type !== \App\Models\Computer::class &&
          $infocom->item_type !== \App\Models\Consumableitem::class &&
          $infocom->item_type !== \App\Models\Dcroom::class &&
          $infocom->item_type !== \App\Models\Domain::class &&
          $infocom->item_type !== \App\Models\Enclosure::class &&
          $infocom->item_type !== \App\Models\ItemDevicesimcard::class &&
          $infocom->item_type !== \App\Models\Line::class &&
          $infocom->item_type !== \App\Models\Networkequipment::class &&
          $infocom->item_type !== \App\Models\Passivedcequipment::class &&
          $infocom->item_type !== \App\Models\Computer::class &&
          $infocom->item_type !== \App\Models\Pdu::class &&
          $infocom->item_type !== \App\Models\Peripheral::class &&
          $infocom->item_type !== \App\Models\Phone::class &&
          $infocom->item_type !== \App\Models\Printer::class &&
          $infocom->item_type !== \App\Models\Rack::class &&
          $infocom->item_type !== \App\Models\Software::class &&
          $infocom->item_type !== \App\Models\Softwarelicense::class
      )
      {
        throw new \Exception('Model not allowed', 400);
      }

      $item_type = $infocom->item_type;
      $item3 = new $item_type();
      $myItem3 = $item3->where('id', $infocom->item_id)->first();
      if ($myItem3 !== null)
      {
        if ($myItem3->entity !== null)
        {
          $entity_id = $myItem3->entity->id;
          $entity = $myItem3->entity->completename;
          $entity_url = '';
          if (array_key_exists($entity_id, $myBudgetMain) !== true)
          {
            $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $myItem3->entity->id);

            $myBudgetMain[$entity_id] = [
              'name'        => $entity,
              'entity_url'  => $entity_url,
              'items'       => [],
              'total'       => 0,
            ];
          }

          $type_fr = $item3->getTitle();
          $type = $item3->getTable();

          if (array_key_exists($type, $myBudgetMainType) !== true)
          {
            $myBudgetMainType[$type] = [
              'name'  => $type_fr,
              'total' => 0,
            ];
          }

          if (array_key_exists($type, $myBudgetMain[$entity_id]['items']) !== true)
          {
            $myBudgetMain[$entity_id]['items'][$type] = [
              'total' => 0,
            ];
          }

          $value = $this->showCosts($infocom->value);

          $myBudgetMain[$entity_id]['total'] = $myBudgetMain[$entity_id]['total'] + $value;
          $myBudgetMainType[$type]['total'] = $myBudgetMainType[$type]['total'] + $value;

          $itemtypetotal = $myBudgetMain[$entity_id]['items'][$type]['total'] + $value;
          $myBudgetMain[$entity_id]['items'][$type]['total'] = $itemtypetotal;
        }
      }
    }

    $contractcosts = \App\Models\Contractcost::where('budget_id', $args['id'])->get();
    foreach ($contractcosts as $contractcost)
    {
      $contract = \App\Models\Contract::where('id', $contractcost->contract_id)->first();
      if ($contract !== null && !is_null($contract->entity))
      {
        $entity_id = $contract->entity->id;
        $entity = $contract->entity->completename;
        if (array_key_exists($entity_id, $myBudgetMain) !== true)
        {
          $myBudgetMain[$entity_id] = [
            'name'    => $entity,
            'items'   => [],
            'total'   => 0,
          ];
        }

        $type_fr = $translator->translatePlural('Contract', 'Contract', 1);
        $type = 'contracts';

        if (array_key_exists($type, $myBudgetMainType) !== true)
        {
          $myBudgetMainType[$type] = [
            'name'  => $type_fr,
            'total' => 0,
          ];
        }

        if (array_key_exists($type, $myBudgetMain[$entity_id]['items']) !== true)
        {
          $myBudgetMain[$entity_id]['items'][$type] = [
            'total' => 0,
          ];
        }

        $value = $this->showCosts($contractcost->cost);

        $myBudgetMain[$entity_id]['total'] = $myBudgetMain[$entity_id]['total'] + $value;
        $myBudgetMainType[$type]['total'] = $myBudgetMainType[$type]['total'] + $value;

        $itemtypetotal = $myBudgetMain[$entity_id]['items'][$type]['total'] + $value;
        $myBudgetMain[$entity_id]['items'][$type]['total'] = $itemtypetotal;
      }
    }

    $ticketcosts = \App\Models\Ticketcost::where('budget_id', $args['id'])->get();
    foreach ($ticketcosts as $ticketcost)
    {
      $ticket = \App\Models\Ticket::where('id', $ticketcost->ticket_id)->first();
      if ($ticket !== null && !is_null($ticket->entity))
      {
        $entity_id = $ticket->entity->id;
        $entity = $ticket->entity->completename;
        if (array_key_exists($entity_id, $myBudgetMain) !== true)
        {
          $myBudgetMain[$entity_id] = [
            'name'    => $entity,
            'items'   => [],
            'total'   => 0,
          ];
        }

        $type_fr = $translator->translatePlural('Ticket', 'Tickets', 1);
        $type = 'tickets';

        if (array_key_exists($type, $myBudgetMainType) !== true)
        {
          $myBudgetMainType[$type] = [
            'name'  => $type_fr,
            'total' => 0,
          ];
        }

        if (array_key_exists($type, $myBudgetMain[$entity_id]['items']) !== true)
        {
          $myBudgetMain[$entity_id]['items'][$type] = [
            'total' => 0,
          ];
        }

        $value = $this->computeTotalCost(
          $ticketcost->actiontime,
          $ticketcost->cost_time,
          $ticketcost->cost_fixed,
          $ticketcost->cost_material
        );

        $myBudgetMain[$entity_id]['total'] = $myBudgetMain[$entity_id]['total'] + $value;
        $myBudgetMainType[$type]['total'] = $myBudgetMainType[$type]['total'] + $value;

        $itemtypetotal = $myBudgetMain[$entity_id]['items'][$type]['total'] + $value;
        $myBudgetMain[$entity_id]['items'][$type]['total'] = $itemtypetotal;
      }
    }

    $item2 = new \App\Models\Problemcost();
    $myItem2 = $item2::where('budget_id', $args['id'])->get();
    foreach ($myItem2 as $current_attacheditem)
    {
      $problem = \App\Models\Problem::where('id', $current_attacheditem->problem_id)->first();
      if ($problem !== null && !is_null($problem->entity))
      {
        $entity_id = $problem->entity->id;
        $entity = $problem->entity->completename;
        if (array_key_exists($entity_id, $myBudgetMain) !== true)
        {
          $myBudgetMain[$entity_id] = [
            'name'    => $entity,
            'items'   => [],
            'total'   => 0,
          ];
        }

        $type_fr = $translator->translatePlural('Problem', 'Problems', 1);
        $type = 'problems';

        if (array_key_exists($type, $myBudgetMainType) !== true)
        {
          $myBudgetMainType[$type] = [
            'name'  => $type_fr,
            'total' => 0,
          ];
        }

        if (array_key_exists($type, $myBudgetMain[$entity_id]['items']) !== true)
        {
          $myBudgetMain[$entity_id]['items'][$type] = [
            'total' => 0,
          ];
        }

        $value = $this->computeTotalCost(
          $current_attacheditem->actiontime,
          $current_attacheditem->cost_time,
          $current_attacheditem->cost_fixed,
          $current_attacheditem->cost_material
        );

        $myBudgetMain[$entity_id]['total'] = $myBudgetMain[$entity_id]['total'] + $value;
        $myBudgetMainType[$type]['total'] = $myBudgetMainType[$type]['total'] + $value;

        $itemtypetotal = $myBudgetMain[$entity_id]['items'][$type]['total'] + $value;
        $myBudgetMain[$entity_id]['items'][$type]['total'] = $itemtypetotal;
      }
    }

    $item2 = new \App\Models\Changecost();
    $myItem2 = $item2::where('budget_id', $args['id'])->get();
    foreach ($myItem2 as $current_attacheditem)
    {
      $change = \App\Models\Change::where('id', $current_attacheditem->change_id)->first();
      if ($change !== null && !is_null($change->entity))
      {
        $entity_id = $change->entity->id;
        $entity = $change->entity->completename;
        if (array_key_exists($entity_id, $myBudgetMain) !== true)
        {
          $myBudgetMain[$entity_id] = [
            'name'    => $entity,
            'items'   => [],
            'total'   => 0,
          ];
        }

        $type_fr = $translator->translatePlural('Change', 'Changes', 1);
        $type = 'changes';

        if (array_key_exists($type, $myBudgetMainType) !== true)
        {
          $myBudgetMainType[$type] = [
            'name'  => $type_fr,
            'total' => 0,
          ];
        }

        if (array_key_exists($type, $myBudgetMain[$entity_id]['items']) !== true)
        {
          $myBudgetMain[$entity_id]['items'][$type] = [
            'total' => 0,
          ];
        }

        $value = $this->computeTotalCost(
          $current_attacheditem->actiontime,
          $current_attacheditem->cost_time,
          $current_attacheditem->cost_fixed,
          $current_attacheditem->cost_material
        );

        $myBudgetMain[$entity_id]['total'] = $myBudgetMain[$entity_id]['total'] + $value;
        $myBudgetMainType[$type]['total'] = $myBudgetMainType[$type]['total'] + $value;

        $itemtypetotal = $myBudgetMain[$entity_id]['items'][$type]['total'] + $value;
        $myBudgetMain[$entity_id]['items'][$type]['total'] = $itemtypetotal;
      }
    }

    $item2 = new \App\Models\Projectcost();
    $myItem2 = $item2::where('budget_id', $args['id'])->get();
    foreach ($myItem2 as $current_attacheditem)
    {
      $item3 = new \App\Models\Project();
      $myItem3 = $item3->where('id', $current_attacheditem->project_id)->first();
      if ($myItem3 !== null && !is_null($myItem3->entity))
      {
        $entity_id = $myItem3->entity->id;
        $entity = $myItem3->entity->completename;
        if (array_key_exists($entity_id, $myBudgetMain) !== true)
        {
          $myBudgetMain[$entity_id] = [
            'name'    => $entity,
            'items'   => [],
            'total'   => 0,
          ];
        }

        $type_fr = $translator->translatePlural('Project', 'Projects', 1);
        $type = 'projects';

        if (array_key_exists($type, $myBudgetMainType) !== true)
        {
          $myBudgetMainType[$type] = [
            'name'  => $type_fr,
            'total' => 0,
          ];
        }

        if (array_key_exists($type, $myBudgetMain[$entity_id]['items']) !== true)
        {
          $myBudgetMain[$entity_id]['items'][$type] = [
            'total' => 0,
          ];
        }

        $value = $this->showCosts($current_attacheditem->cost);

        $myBudgetMain[$entity_id]['total'] = $myBudgetMain[$entity_id]['total'] + $value;
        $myBudgetMainType[$type]['total'] = $myBudgetMainType[$type]['total'] + $value;

        $itemtypetotal = $myBudgetMain[$entity_id]['items'][$type]['total'] + $value;
        $myBudgetMain[$entity_id]['items'][$type]['total'] = $itemtypetotal;
      }
    }

    // tri par ordre alpha
    array_multisort(
      array_column($myBudgetMainType, 'name'),
      SORT_ASC,
      SORT_NATURAL | SORT_FLAG_CASE,
      $myBudgetMainType
    );

    foreach (array_keys($myBudgetMainType) as $type)
    {
      foreach (array_keys($myBudgetMain) as $entity_id)
      {
        if (array_key_exists($type, $myBudgetMain[$entity_id]['items']) !== true)
        {
          $myBudgetMain[$entity_id]['items'][$type] = [
            'total' => 0,
          ];
        }
        else
        {
          $show_cost = $myBudgetMain[$entity_id]['items'][$type]['total'];
          $myBudgetMain[$entity_id]['items'][$type]['total'] = $this->showCosts($show_cost);
        }
      }

      $myBudgetMainType[$type]['total'] = $this->showCosts($myBudgetMainType[$type]['total']);

      $total_spent = $total_spent + $myBudgetMainType[$type]['total'];

      if (stristr($type, 'consumable'))
      {
        $myBudgetMainType[$type]['name'] = $myBudgetMainType[$type]['name'] . ' (' . $type . ')';
      }
    }

    foreach (array_keys($myBudgetMain) as $entity_id)
    {
      $myBudgetMain[$entity_id]['total'] = $this->showCosts($myBudgetMain[$entity_id]['total']);
    }

    $total_remaining = $total_budget - $total_spent;
    $alert_budget = false;
    if ($total_remaining < 0)
    {
      $alert_budget = true;
    }

    $total_spent = $this->showCosts($total_spent);
    $total_remaining = $this->showCosts($total_remaining);

    $colspan = count($myBudgetMainType) + 1;

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('budgetmain', $myBudgetMain);
    $viewData->addData('budgetmaintype', $myBudgetMainType);
    $viewData->addData('total_spent', $total_spent);
    $viewData->addData('total_remaining', $total_remaining);
    $viewData->addData('alert_budget', $alert_budget);
    $viewData->addData('colspan', $colspan);

    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('total', $translator->translate('Total'));
    $viewData->addTranslation('total_spent', $translator->translate('Total spent on the budget'));
    $viewData->addTranslation('total_remaining', $translator->translate('Total remaining on the budget'));

    return $view->render($response, 'subitem/budgetmain.html.twig', (array)$viewData);
  }

    /**
   * @param array<string, string> $args
   */
  public function showSubAttachedItems(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $view = Twig::fromRequest($request);

    $myItem = \App\Models\Budget::where('id', $args['id'])->first();
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
      'itemCartridgeitems',
      'itemCertificates',
      'itemComputers',
      'itemConsumableitems',
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
      'itemRacks',
      'itemSoftwares',
      'itemSoftwarelicenses',
    ];

    foreach ($itemTypes as $itemType)
    {
      $budget = \App\Models\Budget::where('id', $args['id'])->with($itemType)->first();
      if (is_null($budget))
      {
        throw new \Exception('Id not found', 404);
      }

      foreach ($budget->{$itemType} as $relationItem)
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

    $budget = \App\Models\Budget::where('id', $args['id'])->with('itemContracts')->first();
    if (is_null($budget))
    {
      throw new \Exception('Id not found', 404);
    }

    foreach ($budget->itemContracts as $contract)
    {
      $type_fr = $translator->translatePlural('Contract', 'Contract', 1);
      $type = 'contracts';

      $entity = '';
      $entity_url = '';
      if ($contract->entity !== null)
      {
        $entity = $contract->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $contract->entity->id);
      }

      $nom = $contract->name;

      $nom_url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $contract->id);

      $serial = '';

      $otherserial = '';

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

      $domain_relation = '';
      $domain_relation_url = '';

      $value = $this->showCosts($contract->getRelationValue('pivot')->cost);

      if (array_key_exists($contract->id, $myAttachedItems[$type]['items']) !== true)
      {
        $myAttachedItems[$type]['items'][$contract->id] = [
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
      } else {
        $sum = (int) $myAttachedItems[$type]['items'][$contract->id]['value'] + (int) $value;
        $myAttachedItems[$type]['items'][$contract->id]['value'] = $this->showCosts($sum);
      }

      $myAttachedItems[$type]['nb'] = count($myAttachedItems[$type]['items']);
    }

    $budget = \App\Models\Budget::where('id', $args['id'])->with('itemTickets')->first();
    if (is_null($budget))
    {
      throw new \Exception('Id not found', 404);
    }
    foreach ($budget->itemTickets as $ticket)
    {
      $type_fr = $translator->translatePlural('Ticket', 'Tickets', 1);
      $type = 'tickets';

      $entity = '';
      $entity_url = '';
      if ($ticket->entity !== null)
      {
        $entity = $ticket->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $ticket->entity->id);
      }

      $nom = $ticket->name;

      $nom_url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $ticket->id);

      $serial = '';

      $otherserial = '';

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

      $domain_relation = '';
      $domain_relation_url = '';

      $value = $this->computeTotalCost(
        $ticket->getRelationValue('pivot')->actiontime,
        $ticket->getRelationValue('pivot')->cost_time,
        $ticket->getRelationValue('pivot')->cost_fixed,
        $ticket->getRelationValue('pivot')->cost_material
      );

      if (array_key_exists($ticket->id, $myAttachedItems[$type]['items']) !== true)
      {
        $myAttachedItems[$type]['items'][$ticket->id] = [
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
      } else {
        $sum = intval($myAttachedItems[$type]['items'][$ticket->id]['value']) + $value;
        $myAttachedItems[$type]['items'][$ticket->id]['value'] = $this->showCosts($sum);
      }

      $myAttachedItems[$type]['nb'] = count($myAttachedItems[$type]['items']);
    }

    $budget = \App\Models\Budget::where('id', $args['id'])->with('itemProblems')->first();
    if (is_null($budget))
    {
      throw new \Exception('Id not found', 404);
    }
    foreach ($budget->itemProblems as $problem)
    {
      $type_fr = $translator->translatePlural('Problem', 'Problems', 1);
      $type = 'problems';

      $entity = '';
      $entity_url = '';
      if ($problem->entity !== null)
      {
        $entity = $problem->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $problem->entity->id);
      }

      $nom = $problem->name;

      $nom_url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $problem->id);

      $serial = '';

      $otherserial = '';

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

      $domain_relation = '';
      $domain_relation_url = '';

      $value = $this->computeTotalCost(
        $problem->getRelationValue('pivot')->actiontime,
        $problem->getRelationValue('pivot')->cost_time,
        $problem->getRelationValue('pivot')->cost_fixed,
        $problem->getRelationValue('pivot')->cost_material
      );

      if (array_key_exists($problem->id, $myAttachedItems[$type]['items']) !== true)
      {
        $myAttachedItems[$type]['items'][$problem->id] = [
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
      } else {
        $sum = floatval($myAttachedItems[$type]['items'][$problem->id]['value']) + $value;
        $myAttachedItems[$type]['items'][$problem->id]['value'] = $this->showCosts($sum);
      }

      $myAttachedItems[$type]['nb'] = count($myAttachedItems[$type]['items']);
    }

    $budget = \App\Models\Budget::where('id', $args['id'])->with('itemChanges')->first();
    if (is_null($budget))
    {
      throw new \Exception('Id not found', 404);
    }
    foreach ($budget->itemChanges as $change)
    {
      $type_fr = $translator->translatePlural('Change', 'Changes', 1);
      $type = 'changes';

      $entity = '';
      $entity_url = '';
      if ($change->entity !== null)
      {
        $entity = $change->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $change->entity->id);
      }

      $nom = $change->name;

      $nom_url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $change->id);

      $serial = '';

      $otherserial = '';

      $first = false;
      if (array_key_exists($type, $myAttachedItems) !== true)
      {
        $myAttachedItems[$type] = [
          'name' => $type_fr,
          'nb' => 0,
          'items' => [],
        ];

        $first = true;
      }

      $status = '';

      $domain_relation = '';
      $domain_relation_url = '';

      $value = $this->computeTotalCost(
        $change->getRelationValue('pivot')->actiontime,
        $change->getRelationValue('pivot')->cost_time,
        $change->getRelationValue('pivot')->cost_fixed,
        $change->getRelationValue('pivot')->cost_material
      );

      if (array_key_exists($change->id, $myAttachedItems[$type]['items']) !== true)
      {
        $myAttachedItems[$type]['items'][$change->id] = [
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
      } else {
        $sum = floatval($myAttachedItems[$type]['items'][$change->id]['value']) + $value;
        $myAttachedItems[$type]['items'][$change->id]['value'] = $this->showCosts($sum);
      }

      $myAttachedItems[$type]['nb'] = count($myAttachedItems[$type]['items']);
    }

    $budget = \App\Models\Budget::where('id', $args['id'])->with('itemProjects')->first();
    if (is_null($budget))
    {
      throw new \Exception('Id not found', 404);
    }
    foreach ($budget->itemProjects as $project)
    {
      $type_fr = $translator->translatePlural('Project', 'Projects', 1);
      $type = 'projects';

      $entity = '';
      $entity_url = '';
      if ($project->entity !== null)
      {
        $entity = $project->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $project->entity->id);
      }

      $nom = $project->name;

      $nom_url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $project->id);

      $serial = '';

      $otherserial = '';

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

      $domain_relation = '';
      $domain_relation_url = '';

      $value = intval($this->showCosts($project->getRelationValue('pivot')->cost));

      if (array_key_exists($project->id, $myAttachedItems[$type]['items']) !== true)
      {
        $myAttachedItems[$type]['items'][$project->id] = [
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
      } else {
        $sum = intval($myAttachedItems[$type]['items'][$project->id]['value']) + $value;
        $myAttachedItems[$type]['items'][$project->id]['value'] = $this->showCosts($sum);
      }

      $myAttachedItems[$type]['nb'] = count($myAttachedItems[$type]['items']);
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
