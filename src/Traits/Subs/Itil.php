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
    global $translator;

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
      $item4 = new \App\Models\ItemTicket();
      $myItem4 = $item4::where('ticket_id', $ticket->id)->get();
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

    $viewData->addTranslation('tickets', $translator->translatePlural('Ticket', 'Tickets', 2));
    $viewData->addTranslation('problems', $translator->translatePlural('Problem', 'Problems', 2));
    $viewData->addTranslation('changes', $translator->translatePlural('Change', 'Changes', 2));
    $viewData->addTranslation(
      'tickets_link_elements',
      $translator->translatePlural('Ticket on linked items', 'Tickets on linked items', 1)
    );
    $viewData->addTranslation('problems_link_elements', $translator->translate('Problems on linked items'));
    $viewData->addTranslation('changes_link_elements', $translator->translate('Changes on linked items'));
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
    $viewData->addTranslation('no_problem_found', $translator->translate('No problem found.'));
    $viewData->addTranslation('no_change_found', $translator->translate('No change found.'));

    return $view->render($response, 'subitem/itil.html.twig', (array)$viewData);
  }

  /**
   * @return array<mixed>
   */
  public static function getStatusArray(): array
  {
    global $translator;
    return [
      1 => [
        'title' => $translator->translate('New'),
        'displaystyle' => 'marked',
        'color' => 'olive',
        'icon'  => 'book open',
      ],
      2 => [
        'title' => $translator->translate('status' . "\004" . 'Processing (assigned)'),
        'displaystyle' => 'marked',
        'color' => 'blue',
        'icon'  => 'book reader',
      ],
      3 => [
        'title' => $translator->translate('status' . "\004" . 'Processing (planned)'),
        'displaystyle' => 'marked',
        'color' => 'blue',
        'icon'  => 'business time',
      ],
      4 => [
        'title' => $translator->translate('Pending'),
        'displaystyle' => 'marked',
        'color' => 'grey',
        'icon'  => 'pause',
      ],
      5 => [
        'title' => $translator->translate('Solved'),
        'displaystyle' => 'marked',
        'color' => 'purple',
        'icon'  => 'vote yea',
      ],
      6 => [
        'title' => $translator->translate('Closed'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      7 => [
        'title' => $translator->translate('status' . "\004" . 'Accepted'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      8 => [
        'title' => $translator->translate('Review'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      9 => [
        'title' => $translator->translate('Evaluation'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      10 => [
        'title' => $translator->translatePlural('Approval', 'Approvals', 1),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      11 => [
        'title' => $translator->translate('change' . "\004" . 'Testing'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      12 => [
        'title' => $translator->translate('Qualification'),
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
    global $translator;
    return [
      6 => [
        'title' => $translator->translate('priority' . "\004" . 'Major'),
        'color' => 'fusionmajor',
        'icon'  => 'fire extinguisher',
      ],
      5 => [
        'title' => $translator->translate('priority' . "\004" . 'Very high'),
        'color' => 'fusionveryhigh',
        'icon'  => 'fire alternate',
      ],
      4 => [
        'title' => $translator->translate('priority' . "\004" . 'High'),
        'color' => 'fusionhigh',
        'icon'  => 'fire',
      ],
      3 => [
        'title' => $translator->translate('priority' . "\004" . 'Medium'),
        'color' => 'fusionmedium',
        'icon'  => 'volume up',
      ],
      2 => [
        'title' => $translator->translate('priority' . "\004" . 'Low'),
        'color' => 'fusionlow',
        'icon'  => 'volume down',
      ],
      1 => [
        'title' => $translator->translate('priority' . "\004" . 'Very low'),
        'color' => 'fusionverylow',
        'icon'  => 'volume off',
      ],
    ];
  }
}
