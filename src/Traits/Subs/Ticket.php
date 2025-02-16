<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Ticket
{
  /**
   * @param array<string, string> $args
   */
  public function showSubTickets(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $myItem2 = [];
    if ($this->choose == 'changes')
    {
      $item2 = new \App\Models\ChangeTicket();
      $myItem2 = $item2::where('change_id', $args['id'])->get();
    }
    if ($this->choose == 'problems')
    {
      $item2 = new \App\Models\ProblemTicket();
      $myItem2 = $item2::where('problem_id', $args['id'])->get();
    }
    if ($this->choose == 'projecttasks')
    {
      $item2 = new \App\Models\ProjecttaskTicket();
      $myItem2 = $item2::where('projecttask_id', $args['id'])->get();
    }

    $rootUrl = $this->genereRootUrl($request, '/tickets');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $tickets = [];
    foreach ($myItem2 as $current_item)
    {
      $item3 = new \App\Models\Ticket();
      $myItem3 = $item3->where('id', $current_item->ticket_id)->first();
      if ($myItem3 !== null)
      {
        $url = $this->genereRootUrl2Link($rootUrl2, '/tickets/', $myItem3->id);

        $status = $this->getStatusArray()[$myItem3->status];

        $entity = '';
        $entity_url = '';
        if ($myItem3->entity !== null)
        {
          $entity = $myItem3->entity->completename;
          $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $myItem3->entity->id);
        }

        $priority = $this->getPriorityArray()[$myItem3->priority];

        $requesters = [];
        if ($myItem3->requester !== null)
        {
          foreach ($myItem3->requester as $requester)
          {
            $requester_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $requester->id);

            $requesters[] = [
              'url'   => $requester_url,
              'name'  => $this->genereUserName($requester->name, $requester->lastname, $requester->firstname),
            ];
          }
        }
        if ($myItem3->requestergroup !== null)
        {
          foreach ($myItem3->requestergroup as $requestergroup)
          {
            $requester_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $requestergroup->id);

            $requesters[] = [
              'url'   => $requester_url,
              'name'  => $requestergroup->completename,
            ];
          }
        }

        $technicians = [];
        if ($myItem3->technician !== null)
        {
          foreach ($myItem3->technician as $technician)
          {
            $technician_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $technician->id);

            $technicians[] = [
              'url'   => $technician_url,
              'name'  => $this->genereUserName($technician->name, $technician->lastname, $technician->firstname),
            ];
          }
        }
        if ($myItem3->techniciangroup !== null)
        {
          foreach ($myItem3->techniciangroup as $techniciangroup)
          {
            $technician_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $techniciangroup->id);

            $technicians[] = [
              'url'   => $technician_url,
              'name'  => $techniciangroup->completename,
            ];
          }
        }

        $associated_items = [];
        $item4 = new \App\Models\ItemTicket();
        $myItem4 = $item4::where('ticket_id', $current_item->ticket_id)->get();
        foreach ($myItem4 as $val)
        {
          $item5 = new $val->item_type();
          $myItem5 = $item5->where('id', $val->item_id)->first();
          if ($myItem5 !== null)
          {
            $type5_fr = $item5->getTitle();
            $type5 = $item5->getTable();

            $name5 = $myItem5->name;

            $url5 = $this->genereRootUrl2Link($rootUrl2, '/' . $type5 . '/', $myItem5->id);

            if ($type5_fr != '')
            {
              $type5_fr = $type5_fr . ' - ';
            }

            $associated_items[] = [
              'type'     => $type5_fr,
              'name'     => $name5,
              'url'      => $url5,
            ];
          }
        }

        if (empty($associated_items))
        {
          $associated_items[] = [
            'type'     => '',
            'name'     => $translator->translate('General'),
            'url'      => '',
          ];
        }

        $category = '';
        $category_url = '';
        if ($myItem3->category !== null)
        {
          $category = $myItem3->category->name;
          $category_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/categories/', $myItem3->category->id);
        }

        $planification = 0; // TODO

        $tickets[$myItem3->id] = [
          'url'                 => $url,
          'status'              => $status,
          'date'                => $myItem3->created_at,
          'last_update'         => $myItem3->updated_at,
          'entity'              => $entity,
          'entity_url'          => $entity_url,
          'priority'            => $priority,
          'requesters'          => $requesters,
          'technicians'         => $technicians,
          'associated_items'    => $associated_items,
          'title'               => $myItem3->name,
          'category'            => $category,
          'category_url'        => $category_url,
          'planification'       => $planification,
        ];
      }
    }

    // tri de la + récente à la + ancienne
    array_multisort(array_column($tickets, 'last_update'), SORT_DESC, SORT_NATURAL | SORT_FLAG_CASE, $tickets);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('tickets', $tickets);

    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('date', $translator->translatePlural('Date', 'Dates', 1));
    $viewData->addTranslation('last_update', $translator->translate('Last update'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('priority', $translator->translate('Priority'));
    $viewData->addTranslation('requesters', $translator->translatePlural('Requester', 'Requesters', 1));
    $viewData->addTranslation('technicians', $translator->translate('Assigned'));
    $viewData->addTranslation(
      'associated_items',
      $translator->translatePlural('Associated element', 'Associated elements', 2)
    );
    $viewData->addTranslation('category', $translator->translate('Category'));
    $viewData->addTranslation('title', $translator->translate('Title'));
    $viewData->addTranslation('planification', $translator->translate('Planification'));
    $viewData->addTranslation('no_ticket_found', $translator->translate('No ticket found.'));

    return $view->render($response, 'subitem/itiltickets.html.twig', (array)$viewData);
  }
}
