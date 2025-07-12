<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Cost
{
  /**
   * @param array<string, string> $args
   */
  public function showSubCosts(Request $request, Response $response, array $args): Response
  {
    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('costs')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/costs');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myCosts = [];
    $myTicketCosts = [];
    $total_cost = 0.0;
    $total_actiontime = 0.0;
    $total_cost_time = 0.0;
    $total_cost_fixed = 0.0;
    $total_cost_material = 0.0;
    $ticket_costs_total_cost = 0;
    $ticket_costs_total_actiontime = 0;
    $ticket_costs_total_cost_time = 0;
    $ticket_costs_total_cost_fixed = 0;
    $ticket_costs_total_cost_material = 0;
    $total_costs = 0;
    foreach ($myItem->costs as $current_cost)
    {
      $budget = '';
      $budget_url = '';
      if ($current_cost->budget !== null)
      {
        $budget = $current_cost->budget->name;
        $budget_url = $this->genereRootUrl2Link($rootUrl2, '/budgets/', $current_cost->budget->id);
      }

      $cost = 0;
      $actiontime = 0.0;
      $cost_time = 0.0;
      $cost_fixed = 0.0;
      $cost_material = 0.0;
      if (($this->choose == 'tickets') || ($this->choose == 'problems') || ($this->choose == 'changes'))
      {
        if (isset($current_cost->actiontime))
        {
          $actiontime = floatval($current_cost->actiontime);

          $total_actiontime = $total_actiontime + $actiontime;
        }
        if (isset($current_cost->cost_time))
        {
          $cost_time = $current_cost->cost_time;

          $total_cost_time = $total_cost_time + $this->computeCostTime($actiontime, $cost_time);
        }
        if (isset($current_cost->cost_fixed))
        {
          $cost_fixed = $current_cost->cost_fixed;

          $total_cost_fixed = $total_cost_fixed + ($cost_fixed);
        }
        if (isset($current_cost->cost_material))
        {
          $cost_material = $current_cost->cost_material;

          $total_cost_material = $total_cost_material + ($cost_material);
        }

        $cost = $this->computeTotalCost($actiontime, $cost_time, $cost_fixed, $cost_material);
        $total_cost = $total_cost + ($cost);
      } else {
        if (isset($current_cost->cost))
        {
          $cost = $current_cost->cost;

          $total_cost = $total_cost + ($cost);
        }
      }

      $myCosts[$current_cost->id] = [
        'name'               => $current_cost->name,
        'begin_date'         => $current_cost->begin_date,
        'end_date'           => $current_cost->end_date,
        'budget'             => $budget,
        'budget_url'         => $budget_url,
        'cost'               => $this->showCosts($cost),
        'actiontime'         => $this->timestampToString($actiontime, false),
        'cost_time'          => $this->showCosts($cost_time),
        'cost_fixed'         => $this->showCosts($cost_fixed),
        'cost_material'      => $this->showCosts($cost_material),
      ];
    }

    // tri de la + récente à la + ancienne
    array_multisort(
      array_column($myCosts, 'begin_date'),
      SORT_DESC,
      SORT_NATURAL | SORT_FLAG_CASE,
      $myCosts
    );

    if ($this->choose == 'projects')
    {
      $item2 = $this->instanciateModel();
      if (get_class($item2) == \App\Models\Project::class)
      {
        $myItem2 = $item2->with('tasks')->where('id', $args['id'])->first();
        if (is_null($myItem2))
        {
          throw new \Exception('Id not found', 404);
        }

        foreach ($myItem2->tasks as $current_task)
        {
          if ($current_task->tickets !== null)
          {
            foreach ($current_task->tickets as $current_ticket)
            {
              $ticket = $current_ticket->name;
              $ticket_url = $this->genereRootUrl2Link($rootUrl2, '/tickets/', $current_ticket->id);

              if ($current_ticket->costs !== null)
              {
                foreach ($current_ticket->costs as $current_cost)
                {
                  $budget = '';
                  $budget_url = '';
                  if ($current_cost->budget !== null)
                  {
                    $budget = $current_cost->budget->name;
                    $budget_url = $this->genereRootUrl2Link($rootUrl2, '/budgets/', $current_cost->budget->id);
                  }

                  $cost = 0;
                  $actiontime = 0;
                  $cost_time = 0;
                  $cost_fixed = 0;
                  $cost_material = 0;
                  if (isset($current_cost->actiontime))
                  {
                    $actiontime = $current_cost->actiontime;

                    $ticket_costs_total_actiontime = $ticket_costs_total_actiontime + $actiontime;
                  }
                  if (isset($current_cost->cost_time))
                  {
                    $cost_time = $current_cost->cost_time;

                    $ticket_costs_total_cost_time =
                      $ticket_costs_total_cost_time + $this->computeCostTime($actiontime, $cost_time);
                  }
                  if (isset($current_cost->cost_fixed))
                  {
                    $cost_fixed = $current_cost->cost_fixed;

                    $ticket_costs_total_cost_fixed = $ticket_costs_total_cost_fixed + ($cost_fixed);
                  }
                  if (isset($current_cost->cost_material))
                  {
                    $cost_material = $current_cost->cost_material;

                    $ticket_costs_total_cost_material = $ticket_costs_total_cost_material + ($cost_material);
                  }

                  $cost = $this->computeTotalCost($actiontime, $cost_time, $cost_fixed, $cost_material);
                  $ticket_costs_total_cost = $ticket_costs_total_cost + ($cost);

                  $myTicketCosts[$current_cost->id] = [
                    'ticket'             => $ticket,
                    'ticket_url'         => $ticket_url,
                    'name'               => $current_cost->name,
                    'begin_date'         => $current_cost->begin_date,
                    'end_date'           => $current_cost->end_date,
                    'budget'             => $budget,
                    'budget_url'         => $budget_url,
                    'cost'               => $this->showCosts($cost),
                    'actiontime'         => $this->timestampToString($actiontime, false),
                    'cost_time'          => $this->showCosts($cost_time),
                    'cost_fixed'         => $this->showCosts($cost_fixed),
                    'cost_material'      => $this->showCosts($cost_material),
                  ];
                }
              }
            }
          }
        }
      }

      // tri de la + récente à la + ancienne
      array_multisort(
        array_column($myTicketCosts, 'begin_date'),
        SORT_DESC,
        SORT_NATURAL | SORT_FLAG_CASE,
        $myTicketCosts
      );

      $total_costs = $total_cost + $ticket_costs_total_cost;
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('costs', $myCosts);
    $viewData->addData('ticket_costs', $myTicketCosts);
    $viewData->addData('total_cost', $this->showCosts($total_cost));
    $viewData->addData('total_actiontime', $this->timestampToString($total_actiontime, false));
    $viewData->addData('total_cost_time', $this->showCosts($total_cost_time));
    $viewData->addData('total_cost_fixed', $this->showCosts($total_cost_fixed));
    $viewData->addData('total_cost_material', $this->showCosts($total_cost_material));
    $viewData->addData('ticket_costs_total_cost', $this->showCosts($ticket_costs_total_cost));
    $viewData->addData(
      'ticket_costs_total_actiontime',
      $this->timestampToString($ticket_costs_total_actiontime, false)
    );
    $viewData->addData('ticket_costs_total_cost_time', $this->showCosts($ticket_costs_total_cost_time));
    $viewData->addData('ticket_costs_total_cost_fixed', $this->showCosts($ticket_costs_total_cost_fixed));
    $viewData->addData('ticket_costs_total_cost_material', $this->showCosts($ticket_costs_total_cost_material));
    $viewData->addData('total_costs', $this->showCosts($total_costs));
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('name', pgettext('global', 'Name'));
    $viewData->addTranslation('begin_date', pgettext('global', 'Start date'));
    $viewData->addTranslation('end_date', pgettext('global', 'End date'));
    $viewData->addTranslation('budget', npgettext('global', 'Budget', 'Budgets', 1));
    $viewData->addTranslation('cost', npgettext('global', 'Cost', 'Costs', 1));
    $viewData->addTranslation('costs', npgettext('global', 'Cost', 'Costs', 2));
    $viewData->addTranslation('total_cost', pgettext('cost', 'Total cost'));
    $viewData->addTranslation('total', pgettext('global', 'Total'));
    $viewData->addTranslation('actiontime', pgettext('cost', 'Duration'));
    $viewData->addTranslation('cost_time', pgettext('cost', 'Time cost'));
    $viewData->addTranslation('cost_fixed', pgettext('cost', 'Fixed cost'));
    $viewData->addTranslation('cost_material', pgettext('cost', 'Material cost'));
    $viewData->addTranslation('ticket_costs', npgettext('cost', 'Ticket cost', 'Ticket costs', 2));
    $viewData->addTranslation('ticket', npgettext('ticket', 'Ticket', 'Tickets', 1));

    return $view->render($response, 'subitem/costs.html.twig', (array)$viewData);
  }
}
