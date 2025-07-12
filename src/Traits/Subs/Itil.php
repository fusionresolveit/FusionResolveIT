<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Itil
{
  /**
   * @param array<string, string> $args
   */
  public function showSubItil(Request $request, Response $response, array $args): Response
  {
    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('tickets', 'problems', 'changes')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/itil');
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
            'url' => $requester_url,
            'name' => $this->genereUserName($requester->name, $requester->lastname, $requester->firstname),
          ];
        }
      }
      if ($ticket->requestergroup !== null)
      {
        foreach ($ticket->requestergroup as $requestergroup)
        {
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $requestergroup->id);

          $requesters[] = [
            'url' => $requester_url,
            'name' => $requestergroup->completename,
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
            'url' => $technician_url,
            'name' => $this->genereUserName($technician->name, $technician->lastname, $technician->firstname),
          ];
        }
      }
      if ($ticket->techniciangroup !== null)
      {
        foreach ($ticket->techniciangroup as $techniciangroup)
        {
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $techniciangroup->id);

          $technicians[] = [
            'url' => $technician_url,
            'name' => $techniciangroup->completename,
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
        'url'               => $url,
        'status'            => $status,
        'date'              => $ticket->created_at,
        'last_update'       => $ticket->updated_at,
        'entity'            => $entity,
        'entity_url'        => $entity_url,
        'priority'          => $priority,
        'requesters'        => $requesters,
        'technicians'       => $technicians,
        'associated_items'  => $associated_items,
        'title'             => $ticket->name,
        'category'          => $category,
        'category_url'      => $category_url,
        'planification'     => $planification,
      ];
    }

    $problems = [];
    foreach ($myItem->problems as $problem)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/problems/', $problem->id);

      $status = $this->getStatusArray()[$problem->status];

      $entity = '';
      $entity_url = '';
      if ($problem->entity !== null)
      {
        $entity = $problem->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $problem->entity->id);
      }

      $priority = $this->getPriorityArray()[$problem->priority];

      $requesters = [];
      if ($problem->requester !== null)
      {
        foreach ($problem->requester as $requester)
        {
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $requester->id);

          $requesters[] = [
            'url' => $requester_url,
            'name' => $this->genereUserName($requester->name, $requester->lastname, $requester->firstname),
          ];
        }
      }
      if ($problem->requestergroup !== null)
      {
        foreach ($problem->requestergroup as $requestergroup)
        {
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $requestergroup->id);

          $requesters[] = [
            'url' => $requester_url,
            'name' => $requestergroup->completename,
          ];
        }
      }

      $technicians = [];
      if ($problem->technician !== null)
      {
        foreach ($problem->technician as $technician)
        {
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $technician->id);

          $technicians[] = [
            'url' => $technician_url,
            'name' => $this->genereUserName($technician->name, $technician->lastname, $technician->firstname),
          ];
        }
      }
      if ($problem->techniciangroup !== null)
      {
        foreach ($problem->techniciangroup as $techniciangroup)
        {
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $techniciangroup->id);

          $technicians[] = [
            'url' => $technician_url,
            'name' => $techniciangroup->completename,
          ];
        }
      }

      $category = '';
      $category_url = '';
      if ($problem->category !== null)
      {
        $category = $problem->category->name;
        $category_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/categories/', $problem->category->id);
      }

      $planification = 0; // TODO

      $problems[$problem->id] = [
        'url'               => $url,
        'status'            => $status,
        'date'              => $problem->created_at,
        'last_update'       => $problem->updated_at,
        'entity'            => $entity,
        'entity_url'        => $entity_url,
        'priority'          => $priority,
        'requesters'        => $requesters,
        'technicians'       => $technicians,
        'title'             => $problem->name,
        'category'          => $category,
        'category_url'      => $category_url,
        'planification'     => $planification,
      ];
    }

    $changes = [];
    foreach ($myItem->changes as $change)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/changes/', $change->id);

      $status = $this->getStatusArray()[$change->status];

      $entity = '';
      $entity_url = '';
      if ($change->entity !== null)
      {
        $entity = $change->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $change->entity->id);
      }

      $priority = $this->getPriorityArray()[$change->priority];

      $requesters = [];
      if ($change->requester !== null)
      {
        foreach ($change->requester as $requester)
        {
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $requester->id);

          $requesters[] = [
            'url' => $requester_url,
            'name' => $this->genereUserName($requester->name, $requester->lastname, $requester->firstname),
          ];
        }
      }
      if ($change->requestergroup !== null)
      {
        foreach ($change->requestergroup as $requestergroup)
        {
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $requestergroup->id);

          $requesters[] = [
            'url' => $requester_url,
            'name' => $requestergroup->completename,
          ];
        }
      }

      $technicians = [];
      if ($change->technician !== null)
      {
        foreach ($change->technician as $technician)
        {
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $technician->id);

          $technicians[] = [
            'url' => $technician_url,
            'name' => $this->genereUserName($technician->name, $technician->lastname, $technician->firstname),
          ];
        }
      }
      if ($change->techniciangroup !== null)
      {
        foreach ($change->techniciangroup as $techniciangroup)
        {
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $techniciangroup->id);

          $technicians[] = [
            'url' => $technician_url,
            'name' => $techniciangroup->completename,
          ];
        }
      }

      $category = '';
      $category_url = '';
      if ($change->category !== null)
      {
        $category = $change->category->name;
        $category_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/categories/', $change->category->id);
      }

      $planification = 0; // TODO

      $changes[$change->id] = [
        'url'               => $url,
        'status'            => $status,
        'date'              => $change->created_at,
        'last_update'       => $change->updated_at,
        'entity'            => $entity,
        'entity_url'        => $entity_url,
        'priority'          => $priority,
        'requesters'        => $requesters,
        'technicians'       => $technicians,
        'title'             => $change->name,
        'category'          => $category,
        'category_url'      => $category_url,
        'planification'     => $planification,
      ];
    }

    $tickets_link_elements = [];
    $problems_link_elements = [];
    $changes_link_elements = [];

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('tickets', $tickets);
    $viewData->addData('problems', $problems);
    $viewData->addData('changes', $changes);
    $viewData->addData('tickets_link_elements', $tickets_link_elements);
    $viewData->addData('problems_link_elements', $problems_link_elements);
    $viewData->addData('changes_link_elements', $changes_link_elements);

    $viewData->addTranslation('tickets', npgettext('ticket', 'Ticket', 'Tickets', 2));
    $viewData->addTranslation('problems', npgettext('problem', 'Problem', 'Problems', 2));
    $viewData->addTranslation('changes', npgettext('change', 'Change', 'Changes', 2));
    $viewData->addTranslation(
      'tickets_link_elements',
      npgettext('ITIL', 'Ticket on linked items', 'Tickets on linked items', 1)
    );
    $viewData->addTranslation('problems_link_elements', pgettext('ITIL', 'Problems on linked items'));
    $viewData->addTranslation('changes_link_elements', pgettext('ITIL', 'Changes on linked items'));
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
    $viewData->addTranslation('no_problem_found', pgettext('problem', 'No problem found.'));
    $viewData->addTranslation('no_change_found', pgettext('change', 'No change found.'));

    return $view->render($response, 'subitem/itil.html.twig', (array)$viewData);
  }

  /**
   * @return array<mixed>
   */
  public static function getStatusArray(): array
  {
    return [
      1 => [
        'title' => pgettext('ITIL status', 'New'),
        'displaystyle' => 'marked',
        'color' => 'olive',
        'icon'  => 'book open',
      ],
      2 => [
        'title' => pgettext('general status', 'Processing (assigned)'),
        'displaystyle' => 'marked',
        'color' => 'blue',
        'icon'  => 'book reader',
      ],
      3 => [
        'title' => pgettext('general status', 'Processing (planned)'),
        'displaystyle' => 'marked',
        'color' => 'blue',
        'icon'  => 'business time',
      ],
      4 => [
        'title' => pgettext('ITIL status', 'Pending'),
        'displaystyle' => 'marked',
        'color' => 'grey',
        'icon'  => 'pause',
      ],
      5 => [
        'title' => pgettext('ITIL status', 'Solved'),
        'displaystyle' => 'marked',
        'color' => 'purple',
        'icon'  => 'vote yea',
      ],
      6 => [
        'title' => pgettext('ITIL status', 'Closed'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      7 => [
        'title' => pgettext('general status', 'Accepted'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      8 => [
        'title' => pgettext('ITIL status', 'Review'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      9 => [
        'title' => pgettext('ITIL status', 'Evaluation'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      10 => [
        'title' => npgettext('ITIL', 'Approval', 'Approvals', 1),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      11 => [
        'title' => pgettext('ITIL status', 'Testing'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      12 => [
        'title' => pgettext('ITIL status', 'Qualification'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
    ];
  }

  /**
   * @return array<mixed>
   */
  public static function getPriorityArray(): array
  {
    return [
      6 => [
        'title' => pgettext('ITIL priority', 'Major'),
        'color' => 'fusionmajor',
        'icon'  => 'fire extinguisher',
      ],
      5 => [
        'title' => pgettext('ITIL priority', 'Very high'),
        'color' => 'fusionveryhigh',
        'icon'  => 'fire alternate',
      ],
      4 => [
        'title' => pgettext('ITIL priority', 'High'),
        'color' => 'fusionhigh',
        'icon'  => 'fire',
      ],
      3 => [
        'title' => pgettext('ITIL priority', 'Medium'),
        'color' => 'fusionmedium',
        'icon'  => 'volume up',
      ],
      2 => [
        'title' => pgettext('ITIL priority', 'Low'),
        'color' => 'fusionlow',
        'icon'  => 'volume down',
      ],
      1 => [
        'title' => pgettext('ITIL priority', 'Very low'),
        'color' => 'fusionverylow',
        'icon'  => 'volume off',
      ],
    ];
  }
}
