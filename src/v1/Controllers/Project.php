<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostProject;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Contract;
use App\Traits\Subs\Cost;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Item;
use App\Traits\Subs\Knowbaseitem;
use App\Traits\Subs\Note;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Project extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use Note;
  use Knowbaseitem;
  use Document;
  use Contract;
  use History;
  use Cost;
  use Item;

  protected $model = \App\Models\Project::class;
  protected $rootUrl2 = '/projects/';
  protected $choose = 'projects';

  protected function instanciateModel(): \App\Models\Project
  {
    return new \App\Models\Project();
  }

  /**
   * @return array{
   *          'itemComputers': \App\Models\Computer,
   *          'itemMonitors': \App\Models\Monitor,
   *          'itemNetworkequipments': \App\Models\Networkequipment,
   *          'itemPeripherals': \App\Models\Peripheral,
   *          'itemPhones': \App\Models\Phone,
   *          'itemPrinters': \App\Models\Printer,
   *          'itemSoftwares': \App\Models\Software,
   *         }
   */
  protected function modelsForSubItem()
  {
    return [
      'itemComputers'         => new \App\Models\Computer(),
      'itemMonitors'          => new \App\Models\Monitor(),
      'itemNetworkequipments' => new \App\Models\Networkequipment(),
      'itemPeripherals'       => new \App\Models\Peripheral(),
      'itemPhones'            => new \App\Models\Phone(),
      'itemPrinters'          => new \App\Models\Printer(),
      'itemSoftwares'         => new \App\Models\Software(),
    ];
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostProject((object) $request->getParsedBody());

    $project = new \App\Models\Project();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($project))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $project = \App\Models\Project::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The project has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($project, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/projects/' . $project->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/projects')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostProject((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $project = \App\Models\Project::where('id', $id)->first();
    if (is_null($project))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($project))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $project->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The project has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($project, 'update');

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
    $project = \App\Models\Project::withTrashed()->where('id', $id)->first();
    if (is_null($project))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($project->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $project->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The project has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/projects')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $project->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The project has been soft deleted successfully');
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
    $project = \App\Models\Project::withTrashed()->where('id', $id)->first();
    if (is_null($project))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($project->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $project->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The project has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubProjecttasks(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Project();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('tasks')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/projecttasks');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myProjecttasks = [];
    foreach ($myItem->tasks as $task)
    {
      $name = $task->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/projecttasks/', $task->id);

      $type = '';
      $type_url = '';
      if ($task->type !== null)
      {
        $type = $task->type->name;
        $type_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/projecttasktypes/', $task->type->id);
      }

      $status = '';
      $status_color = '';
      if ($task->state !== null)
      {
        $status = $task->state->name;
        $status_color = $task->state->color;
      }

      $percent_done = $task->percent_done;
      $percent_done = $this->getValueWithUnit($percent_done, '%', 0);

      $planned_start_date = $task->plan_start_date;

      $planned_end_date = $task->plan_end_date;

      $planned_duration = $this->timestampToString($task->planned_duration, false);

      $effective_duration = $this->timestampToString($task->effective_duration, false);

      $father = '';
      $father_url = '';
      if ($task->parent !== null)
      {
        $father = $task->parent->name;
        $father_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/projecttasks/', $task->parent->id);
      }

      $myProjecttasks[] = [
        'name'                  => $name,
        'url'                   => $url,
        'type'                  => $type,
        'type_url'              => $type_url,
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
    $viewData->addData('show', $this->choose);

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

  /**
   * @param array<string, string> $args
   */
  public function showSubProjects(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Project();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('parents')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/projects');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myProjects = [];
    foreach ($myItem->parents as $parent)
    {
      $name = $parent->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/projects/', $parent->id);

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
      $entity_url = '';
      if ($parent->entity !== null)
      {
        $entity = $parent->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $parent->entity->id);
      }

      $priority = $this->getPriorityArray()[$parent->priority];

      $manager = '';
      $manager_url = '';
      if ($parent->user !== null)
      {
        $manager = $this->genereUserName($parent->user->name, $parent->user->lastname, $parent->user->firstname);
        $manager_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $parent->user->id);
      }
      $manager_group = '';
      $manager_group_url = '';
      if ($parent->group !== null)
      {
        $manager_group = $parent->group->completename;
        $manager_group_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $parent->group->id);
      }

      $myProjects[] = [
        'name'                => $name,
        'url'                 => $url,
        'status'              => $status,
        'status_color'        => $status_color,
        'open_date'           => $open_date,
        'last_update'         => $last_update,
        'entity'              => $entity,
        'entity_url'          => $entity_url,
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

  /**
   * @param array<string, string> $args
   */
  public function showSubProjectteams(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Project();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $item2 = new \App\Models\Projectteam();
    $myItem2 = $item2::where('project_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/projectteams');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myProjectteams = [];
    foreach ($myItem2 as $current_item)
    {
      $item3 = new $current_item->item_type();
      $myItem3 = $item3->where('id', $current_item->item_id)->first();

      if ($myItem3 !== null)
      {
        $type_fr = $item3->getTitle();
        $type = $item3->getTable();

        $name = $myItem3->name;
        if (isset($myItem3->firstname))
        {
          $name = $name . ' ' . $myItem3->firstname;
        }
        if ($name == '')
        {
          $name = '(' . $myItem3->id . ')';
        }

        $url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $myItem3->id);

        $myProjectteams[] = [
          'member'   => $name,
          'url'      => $url,
          'type'     => $type_fr,
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

  /**
   * @param array<string, string> $args
   */
  public function showSubItilitems(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Project();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }
    $tickets = [];
    $problems = [];
    $changes = [];

    $rootUrl = $this->genereRootUrl($request, '/itilitems');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    // Get tickets
    $myItem = $item->with('itilTickets')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    foreach ($myItem->itilTickets as $ticket)
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
        'title'             => $ticket->name,
        'associated_items'  => $associated_items,
        'category'          => $category,
        'category_url'      => $category_url,
        'planification'     => $planification,
      ];
    }

    // Get problems
    $myItem = $item->with('itilProblems')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    foreach ($myItem->itilProblems as $problem)
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

    // Get problems
    $myItem = $item->with('itilChanges')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    foreach ($myItem->itilChanges as $change)
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

    // tri de la + récente à la + ancienne
    array_multisort(array_column($tickets, 'last_update'), SORT_DESC, SORT_NATURAL | SORT_FLAG_CASE, $tickets);
    array_multisort(array_column($problems, 'last_update'), SORT_DESC, SORT_NATURAL | SORT_FLAG_CASE, $problems);
    array_multisort(array_column($changes, 'last_update'), SORT_DESC, SORT_NATURAL | SORT_FLAG_CASE, $changes);

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
