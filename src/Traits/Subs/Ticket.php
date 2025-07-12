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
    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->with('tickets')->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/tickets');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $tickets = [];
    foreach ($myItem->tickets as $ticket)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/tickets/', $ticket->id);

      $status = $this->getStatusArray()[$ticket->status];

      $entity = '';
      $entity_url = '';
      if ($ticket->entity !== null)
      {
        $entity = $ticket->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $ticket->entity->id);
      }

      $priority = $this->getPriorityArray()[$ticket->priority];

      $requesters = [];
      if ($ticket->requester !== null)
      {
        foreach ($ticket->requester as $requester)
        {
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $requester->id);

          $requesters[] = [
            'url'   => $requester_url,
            'name'  => $this->genereUserName($requester->name, $requester->lastname, $requester->firstname),
          ];
        }
      }
      if ($ticket->requestergroup !== null)
      {
        foreach ($ticket->requestergroup as $requestergroup)
        {
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $requestergroup->id);

          $requesters[] = [
            'url'   => $requester_url,
            'name'  => $requestergroup->completename,
          ];
        }
      }

      $technicians = [];
      if ($ticket->technician !== null)
      {
        foreach ($ticket->technician as $technician)
        {
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $technician->id);

          $technicians[] = [
            'url'   => $technician_url,
            'name'  => $this->genereUserName($technician->name, $technician->lastname, $technician->firstname),
          ];
        }
      }
      if ($ticket->techniciangroup !== null)
      {
        foreach ($ticket->techniciangroup as $techniciangroup)
        {
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $techniciangroup->id);

          $technicians[] = [
            'url'   => $technician_url,
            'name'  => $techniciangroup->completename,
          ];
        }
      }

      $associated_items = [];
      $ctrlTicket = new \App\v1\Controllers\Ticket();
      $modelsForSub = $ctrlTicket->modelsForSubItem();
      foreach (array_keys($modelsForSub) as $relationKey)
      {
        $relTicket = \App\Models\Ticket::where('id', $ticket->id)->with($relationKey)->first();
        if (is_null($relTicket))
        {
          continue;
        }
        foreach ($relTicket->{$relationKey} as $relItem)
        {
          $type5_fr = $relItem->getTitle();
          $type5 = $relItem->getTable();

          $name5 = $relItem->name;

          $url5 = $this->genereRootUrl2Link($rootUrl2, '/' . $type5 . '/', $relItem->id);

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
          'name'     => pgettext('ITIL', 'General'),
          'url'      => '',
        ];
      }

      $category = '';
      $category_url = '';
      if ($ticket->category !== null)
      {
        $category = $ticket->category->name;
        $category_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/categories/', $ticket->category->id);
      }

      $planification = 0; // TODO

      $tickets[$ticket->id] = [
        'url'                 => $url,
        'status'              => $status,
        'date'                => $ticket->created_at,
        'last_update'         => $ticket->updated_at,
        'entity'              => $entity,
        'entity_url'          => $entity_url,
        'priority'            => $priority,
        'requesters'          => $requesters,
        'technicians'         => $technicians,
        'associated_items'    => $associated_items,
        'title'               => $ticket->name,
        'category'            => $category,
        'category_url'        => $category_url,
        'planification'       => $planification,
      ];
    }

    // tri de la + récente à la + ancienne
    array_multisort(array_column($tickets, 'last_update'), SORT_DESC, SORT_NATURAL | SORT_FLAG_CASE, $tickets);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('tickets', $tickets);

    $viewData->addTranslation('status', pgettext('global', 'Status'));
    $viewData->addTranslation('date', npgettext('global', 'Date', 'Dates', 1));
    $viewData->addTranslation('last_update', pgettext('global', 'Last update'));
    $viewData->addTranslation('entity', npgettext('global', 'Entity', 'Entities', 1));
    $viewData->addTranslation('priority', pgettext('ITIL', 'Priority'));
    $viewData->addTranslation('requesters', npgettext('ITIL', 'Requester', 'Requesters', 1));
    $viewData->addTranslation('technicians', pgettext('ITIL', 'Assigned'));
    $viewData->addTranslation(
      'associated_items',
      npgettext('global', 'Associated item', 'Associated items', 2)
    );
    $viewData->addTranslation('category', npgettext('global', 'Category', 'Categories', 1));
    $viewData->addTranslation('title', pgettext('global', 'Title'));
    $viewData->addTranslation('planification', pgettext('ITIL', 'Planification'));
    $viewData->addTranslation('no_ticket_found', pgettext('ticket', 'No ticket found.'));

    return $view->render($response, 'subitem/itiltickets.html.twig', (array)$viewData);
  }
}
