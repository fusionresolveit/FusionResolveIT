<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Project extends Common
{
  protected $model = '\App\Models\Project';
  protected $rootUrl2 = '/projects/';
  protected $costchoose = 'project';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Project();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Project();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Project();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }


  public function showSubProjecttasks(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('tasks')->find($args['id']);

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/projecttasks');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myProjecttasks = [];
    foreach ($myItem->tasks as $task)
    {
      $name = $task->name;

      $url = '';
      if ($rootUrl2 != '') {
        $url = $rootUrl2 . "/projecttasks/" . $task->id;
      }

      $type = '';
      if ($task->type !== null)
      {
        $type = $task->type->name;
      }
      $status = '';
      $status_color = '';
      if ($task->state !== null)
      {
        $status = $task->state->name;
        $status_color = $task->state->color;
      }
      $percent_done = $task->percent_done;
      $planned_start_date = $task->plan_start_date;
      $planned_end_date = $task->plan_end_date;
      $planned_duration = $this->timestampToString($task->planned_duration, false);
      $effective_duration = $this->timestampToString($task->effective_duration, false);
      $father = '';
      $father_url = '';

      if ($task->parent !== null)
      {
        $father = $task->parent->name;
        if ($rootUrl2 != '') {
          $father_url = $rootUrl2 . "/projecttasks/" . $task->parent->id;
        }
      }

      $myProjecttasks[] = [
        'name'                  => $name,
        'url'                   => $url,
        'type'                  => $type,
        'status'                => $status,
        'status_color'          => $status_color,
        'percent_done'          => $percent_done,
        'planned_start_date'    => $planned_start_date,
        'planned_end_date'      => $planned_end_date,
        'planned_duration'      => $planned_duration,
        'effective_duration'    => $effective_duration,
        'father'                => $father,
        'father_url'            => $father_url,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('projecttasks', $myProjecttasks);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('percent_done', $translator->translate('Percent done'));
    $viewData->addTranslation('planned_start_date', $translator->translate('Planned start date'));
    $viewData->addTranslation('planned_end_date', $translator->translate('Planned end date'));
    $viewData->addTranslation('planned_duration', $translator->translate('Planned duration'));
    $viewData->addTranslation('effective_duration', $translator->translate('Effective duration'));
    $viewData->addTranslation('father', $translator->translate('Father'));

    return $view->render($response, 'subitem/projecttasks.html.twig', (array)$viewData);
  }

  public function showSubProjects(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('parents')->find($args['id']);

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/projects');
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myProjects = [];
    foreach ($myItem->parents as $parent)
    {
      $name = $parent->name;

      $url = '';
      if ($rootUrl2 != '') {
        $url = $rootUrl2 . "/projects/" . $parent->id;
      }

      $status = '';
      $status_color = '';
      if ($parent->state !== null)
      {
        $status = $parent->state->name;
        $status_color = $parent->state->color;
      }
      $open_date = $parent->date;
      $last_update = $parent->updated_at;
      $entity = '';
      if ($parent->entity !== null)
      {
        $entity = $parent->entity->name;
      }
      $priority = $this->getPriorityArray()[$parent->priority];

      $manager = '';
      $manager_url = '';
      if ($parent->user !== null)
      {
        $manager = $parent->user->name;
        if ($rootUrl2 != '') {
          $manager_url = $rootUrl2 . "/users/" . $parent->user->id;
        }
      }
      $manager_group = '';
      $manager_group_url = '';
      if ($parent->group !== null)
      {
        $manager_group = $parent->group->name;
        if ($rootUrl2 != '') {
          $manager_group_url = $rootUrl2 . "/groups/" . $parent->group->id;
        }
      }

      $myProjects[] = [
        'name'                => $name,
        'url'                 => $url,
        'status'              => $status,
        'status_color'        => $status_color,
        'open_date'           => $open_date,
        'last_update'         => $last_update,
        'entity'              => $entity,
        'priority'            => $priority,
        'manager'             => $manager,
        'manager_url'         => $manager_url,
        'manager_group'       => $manager_group,
        'manager_group_url'   => $manager_group_url,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('projects', $myProjects);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('open_date', $translator->translatePlural('Date', 'Dates', 1));
    $viewData->addTranslation('last_update', $translator->translate('Last update'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('priority', $translator->translate('Priority'));
    $viewData->addTranslation('manager', $translator->translate('Manager'));
    $viewData->addTranslation('manager_group', $translator->translate('Manager group'));

    return $view->render($response, 'subitem/projects.html.twig', (array)$viewData);
  }

  public function showSubProjectteams(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\Projectteam();
    $myItem2 = $item2::where('project_id', $args['id'])->get();

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/projectteams');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myProjectteams = [];
    foreach ($myItem2 as $current_item)
    {
      $item3 = new $current_item->item_type();
      $myItem3 = $item3->find($current_item->item_id);

      if ($myItem3 != null) {
        $name = $myItem3->name;
        if (isset($myItem3->firstname)) {
          $name = $name . ' ' . $myItem3->firstname;
        }
        if ($name == '') {
          $name = '(' . $myItem3->id . ')';
        }

        $url = '';
        if ($rootUrl2 != '') {
          $table = $item3->getTable();
          if ($table != '') {
            $url = $rootUrl2 . "/" . $table . "/" . $myItem3->id;
          }
        }

        $type = $item3->getTitle();

        $myProjectteams[] = [
          'member'   => $name,
          'url'      => $url,
          'type'     => $type,
        ];
      }
    }


    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('projectteams', $myProjectteams);

    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('member', $translator->translatePlural('Member', 'Members', 2));

    return $view->render($response, 'subitem/projectteams.html.twig', (array)$viewData);
  }

  public function showSubItems(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\Projectitem();
    $myItem2 = $item2::where('project_id', $args['id'])->get();

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/items');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myItems = [];
    foreach ($myItem2 as $appliance_item)
    {
      $item3 = new $appliance_item->item_type();
      $myItem3 = $item3->find($appliance_item->item_id);

      if ($myItem3 != null) {
        $type = $item3->getTitle();

        $name = $myItem3->name;
        if ($name == '') {
          $name = '(' . $myItem3->id . ')';
        }

        $url = '';
        if ($rootUrl2 != '') {
          $table = $item3->getTable();
          if ($table != '') {
            $url = $rootUrl2 . "/" . $table . "/" . $myItem3->id;
          }
        }

        $entity = '';
        if ($myItem3->entity != null) {
          $entity = $myItem3->entity->name;
        }

        $serial_number = $myItem3->serial;
        $inventaire_number = $myItem3->otherserial;

        $myItems[] = [
          'type'                 => $type,
          'name'                 => $name,
          'url'                  => $url,
          'entity'               => $entity,
          'serial_number'        => $serial_number,
          'inventaire_number'    => $inventaire_number,
        ];
      }
    }

    // tri ordre alpha
    usort($myItems, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });
    usort($myItems, function ($a, $b)
    {
      return strtolower($a['type']) > strtolower($b['type']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('items', $myItems);
    $viewData->addData('show', 'project');

    $viewData->addTranslation('type', $translator->translate('Item type'));
    $viewData->addTranslation('name', $translator->translatePlural('Item', 'Items', 1));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('serial_number', $translator->translate('Serial number'));
    $viewData->addTranslation('inventaire_number', $translator->translate('Inventory number'));

    return $view->render($response, 'subitem/items.html.twig', (array)$viewData);
  }

  public function showSubItilitems(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\Itilproject();
    $myItem2 = $item2::where('project_id', $args['id'])->get();

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/itilitems');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $tickets = [];
    $problems = [];
    $changes = [];
    foreach ($myItem2 as $appliance_item)
    {
      $item3 = new $appliance_item->item_type();
      $myItem3 = $item3->find($appliance_item->item_id);

      if ($myItem3 != null) {
        $table = $item3->getTable();

        if ($table == 'changes') {
          $url = '';
          if ($rootUrl2 != '') {
            $url = $rootUrl2 . "/changes/" . $myItem3->id;
          }

          $status = $this->getStatusArray()[$myItem3->status];

          $entity = '';
          if ($myItem3->entity != null) {
            $entity = $myItem3->entity->name;
          }

          $priority = $this->getPriorityArray()[$myItem3->priority];

          $requesters = [];
          if ($myItem3->requester != null) {
            foreach ($myItem3->requester as $requester) {
              $requesters[] = ['name' => $requester->name];
            }
          }
          if ($myItem3->requestergroup != null) {
            foreach ($myItem3->requestergroup as $requestergroup) {
              $requesters[] = ['name' => $requestergroup->completename];
            }
          }
          $technicians = [];
          if ($myItem3->technician != null) {
            foreach ($myItem3->technician as $technician) {
              $technicians[] = ['name' => $technician->name];
            }
          }
          if ($myItem3->techniciangroup != null) {
            foreach ($myItem3->techniciangroup as $techniciangroup) {
              $technicians[] = ['name' => $techniciangroup->completename];
            }
          }
          $category = '';
          if ($myItem3->itilcategorie != null) {
            $category = $myItem3->itilcategorie->name;
          }
          $planification = 0; // TODO

          $changes[$myItem3->id] = [
            'url' => $url,
            'status' => $status,
            'date' => $myItem3->date,
            'last_update' => $myItem3->updated_at,
            'entity' => $entity,
            'priority' => $priority,
            'requesters' => $requesters,
            'technicians' => $technicians,
            'title' => $myItem3->name,
            'category' => $category,
            'planification' => $planification,
          ];
        }
        if ($table == 'problems') {
          $url = '';
          if ($rootUrl2 != '') {
            $url = $rootUrl2 . "/problems/" . $myItem3->id;
          }

          $status = $this->getStatusArray()[$myItem3->status];

          $entity = '';
          if ($myItem3->entity != null) {
            $entity = $myItem3->entity->name;
          }

          $priority = $this->getPriorityArray()[$myItem3->priority];

          $requesters = [];
          if ($myItem3->requester != null) {
            foreach ($myItem3->requester as $requester) {
              $requesters[] = ['name' => $requester->name];
            }
          }

          if ($myItem3->requestergroup != null) {
            foreach ($myItem3->requestergroup as $requestergroup) {
              $requesters[] = ['name' => $requestergroup->completename];
            }
          }

          $technicians = [];
          if ($myItem3->technician != null) {
            foreach ($myItem3->technician as $technician) {
              $technicians[] = ['name' => $technician->name];
            }
          }

          if ($myItem3->techniciangroup != null) {
            foreach ($myItem3->techniciangroup as $techniciangroup) {
              $technicians[] = ['name' => $techniciangroup->completename];
            }
          }

          $category = '';
          if ($myItem3->category != null) {
            $category = $myItem3->category->name;
          }

          $planification = 0; // TODO


          $problems[$myItem3->id] = [
            'url' => $url,
            'status' => $status,
            'date' => $myItem3->date,
            'last_update' => $myItem3->updated_at,
            'entity' => $entity,
            'priority' => $priority,
            'requesters' => $requesters,
            'technicians' => $technicians,
            'title' => $myItem3->name,
            'category' => $category,
            'planification' => $planification,
          ];
        }
        if ($table == 'tickets') {
          $url = '';
          if ($rootUrl2 != '') {
            $url = $rootUrl2 . "/tickets/" . $myItem3->id;
          }

          $status = $this->getStatusArray()[$myItem3->status];

          $entity = '';
          if ($myItem3->entity != null) {
            $entity = $myItem3->entity->name;
          }

          $priority = $this->getPriorityArray()[$myItem3->priority];

          $requesters = [];
          if ($myItem3->requester != null) {
            foreach ($myItem3->requester as $requester) {
              $requesters[] = ['name' => $requester->name];
            }
          }

          if ($myItem3->requestergroup != null) {
            foreach ($myItem3->requestergroup as $requestergroup) {
              $requesters[] = ['name' => $requestergroup->completename];
            }
          }

          $technicians = [];
          if ($myItem3->technician != null) {
            foreach ($myItem3->technician as $technician) {
              $technicians[] = ['name' => $technician->name];
            }
          }

          if ($myItem3->techniciangroup != null) {
            foreach ($myItem3->techniciangroup as $techniciangroup) {
              $technicians[] = ['name' => $techniciangroup->completename];
            }
          }

          $associated_items = []; // TODO

          $category = '';
          if ($myItem3->category != null) {
            $category = $myItem3->category->name;
          }

          $planification = 0; // TODO


          $tickets[$myItem3->id] = [
            'url' => $url,
            'status' => $status,
            'date' => $myItem3->date,
            'last_update' => $myItem3->updated_at,
            'entity' => $entity,
            'priority' => $priority,
            'requesters' => $requesters,
            'technicians' => $technicians,
            'associated_items' => $associated_items,
            'title' => $myItem3->name,
            'category' => $category,
            'planification' => $planification,
          ];
        }
      }
    }

      // tri de la + récente à la + ancienne
    usort($tickets, function ($a, $b)
    {
      return strtolower($a['last_update']) > strtolower($b['last_update']);
    });
    usort($problems, function ($a, $b)
    {
      return strtolower($a['last_update']) > strtolower($b['last_update']);
    });
    usort($changes, function ($a, $b)
    {
      return strtolower($a['last_update']) > strtolower($b['last_update']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('tickets', $tickets);
    $viewData->addData('problems', $problems);
    $viewData->addData('changes', $changes);
    $viewData->addData('show', 'project');

    $viewData->addTranslation('tickets', $translator->translatePlural('Ticket', 'Tickets', 2));
    $viewData->addTranslation('problems', $translator->translatePlural('Problem', 'Problems', 2));
    $viewData->addTranslation('changes', $translator->translatePlural('Change', 'Changes', 2));
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
}
