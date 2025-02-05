<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

final class Ticket extends Common
{
  protected $model = '\App\Models\Ticket';
  protected $rootUrl2 = '/tickets/';
  protected $choose = 'tickets';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Ticket();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Ticket();
    return $this->commonShowITILItem($request, $response, $args, $item);
  }

  public function showNewItem(Request $request, Response $response, $args): Response
  {
    $session = new \SlimSession\Helper();
    $session['ticketCreationDate'] = gmdate('Y-m-d H:i:s');

    $item = new \App\Models\Ticket();
    return $this->commonShowITILNewItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getParsedBody();

    $data = $this->prepareDataSave($data, $args['id']);

    $this->saveItem($data, $args['id']);

    $uri = $request->getUri();
    return $response
      ->withHeader('Location', (string) $uri)
      ->withStatus(302);
  }

  public function runRules($data, $id)
  {
    // Run ticket rules
    $rule = new \App\v1\Controllers\Rules\Ticket();
    if (is_null($id))
    {
      $ticket = new \App\Models\Ticket();
    } else {
      $ticket = \App\Models\Ticket::find($id);
    }

    $preparedData = $rule->prepareData($ticket, $data);
    $ruledData = $rule->processAllRules($ticket, $preparedData);

    $data = $rule->parseNewData($ticket, $data, $ruledData);
    return $data;
  }

  public function showStats(Request $request, Response $response, $args): Response
  {
    global $translator;
    $item = new \App\Models\Ticket();
    $view = Twig::fromRequest($request);

    $myItem = $item::find($args['id']);

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/stats');

    $feeds = [];

    $feeds[] = [
      'date'  => $myItem->created_at,
      'text'  => $translator->translate('Opening date'),
      'icon'  => 'pencil alternate',
      'color' => 'blue'
    ];

    $feeds[] = [
      'date'  => $myItem->time_to_resolve,
      'text'  => $translator->translate('Time to resolve'),
      'icon'  => 'hourglass half',
      'color' => 'blue'
    ];
    if ($myItem->status >= 5)
    {
      $feeds[] = [
        'date'  => $myItem->solved_at,
        'text'  => $translator->translate('Resolution date'),
        'icon'  => 'check circle',
        'color' => 'blue'
      ];
    }
    if ($myItem->status == 6)
    {
      $feeds[] = [
        'date'  => $myItem->closed_at,
        'text'  => $translator->translate('Closing date'),
        'icon'  => 'flag checkered',
        'color' => 'blue'
      ];
    }


    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('feeds', $feeds);


    return $view->render($response, 'subitem/stats.html.twig', (array) $viewData);
  }

  public function showProblem(Request $request, Response $response, $args): Response
  {
    global $translator;
    $item = new \App\Models\Ticket();
    $view = Twig::fromRequest($request);

    $myItem = $item::with(['problems'])->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/problem');

    // $problems = $myItem->problem()->get()->toArray();
    $problems = [];
    foreach ($myItem->problems as $problem)
    {
      // $problems[] = $problem->toArray();

      $problems[] = [
        'id'          => $problem->id,
        'name'        => $problem->name,
        'updated_at'  => $problem->updated_at,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('feeds', $item->getFeeds($args['id']));
    $viewData->addData('content', \App\v1\Controllers\Toolbox::convertMarkdownToHtml($myItem->content));
    $viewData->addData('problems', $problems);

    $viewData->addTranslation('attachItem', $translator->translate('Attach to an existant problem'));
    $viewData->addTranslation('selectItem', $translator->translate('Select problem...'));
    $viewData->addTranslation('buttonAttach', $translator->translate('Attach'));
    $viewData->addTranslation('addItem', $translator->translate('Add new problem'));
    $viewData->addTranslation('buttonCreate', $translator->translate('Create'));
    $viewData->addTranslation('attachedItems', $translator->translate('Problems attached'));
    $viewData->addTranslation('updated', $translator->translate('Last update'));
    $viewData->addTranslation('or', $translator->translate('Ou'));

    return $view->render($response, 'subitem/problem.html.twig', (array) $viewData);
  }

  public function postProblem(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'problem') && is_numeric($data->problem))
    {
      $myItem = \App\Models\Ticket::find($args['id']);
      $myItem->problems()->attach((int)$data->problem);

      // add message to session
      \App\v1\Controllers\Toolbox::addSessionMessage("The ticket has been attached to problem successfully");
    }
    else
    {
      // add message to session
      \App\v1\Controllers\Toolbox::addSessionMessage('Error to attache ticket to problem', 'error');
    }

    $uri = $request->getUri();
    return $response
      ->withHeader('Location', (string) $uri);
  }

  /**
    * Compute Priority
    *
    * @param $urgency   integer from 1 to 5
    * @param $impact    integer from 1 to 5
    *
    * @return integer from 1 to 5 (priority)
   **/
  public static function computePriority($urgency, $impact)
  {
    $priority_matrix = \App\Models\Config::where('context', 'core')->where('name', 'priority_matrix')->first();
    if (!is_null($priority_matrix))
    {
      $matrix = json_decode($priority_matrix->value, true);
      if (isset($matrix[(int) $urgency][(int) $impact]))
      {
        return $matrix[(int) $urgency][(int) $impact];
      }
    }
    // Failback to trivial
    return (int) round(($urgency + $impact) / 2);
  }

  /**
   * Prepare data to save
   *   * manage compute priority
   *   * manage rules
   *   * store in DB the ticket, the users, the groups... so all linked to ticket
   */
  public function prepareDataSave($data, $id = null)
  {
    if (is_null($id))
    {
      $myItem = new \App\Models\Ticket();
    }
    else
    {
      $myItem = \App\Models\Ticket::find($id);
    }
    $definitions = $myItem->getDefinitions();


    if (!property_exists($data, 'priority') || ($myItem->priority == $data->priority))
    {
      if (!property_exists($data, 'impact'))
      {
        $data->impact = 3;
      }

      $data->priority = self::computePriority($data->urgency, $data->impact);
    }

    // TODO rules
    return $data;
  }

  public function showSubItems(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/items');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myItems = [];
    foreach ($myItem->items as $current_item)
    {
      $item3 = new $current_item->item_type();
      $myItem3 = $item3->find($current_item->item_id);
      if ($myItem3 !== null)
      {
        $type_fr = $item3->getTitle();
        $type = $item3->getTable();

        $current_id = $myItem3->id;

        $name = $myItem3->name;
        if (is_null($name) || $name == '')
        {
          $name = '(' . $current_id . ')';
        }

        $url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $current_id);

        $entity = '';
        $entity_url = '';
        if ($myItem3->entity !== null)
        {
          $entity = $myItem3->entity->completename;
          $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $myItem3->entity->id);
        }

        $serial_number = $myItem3->serial;

        $inventaire_number = $myItem3->otherserial;

        $myItems[] = [
          'type'                 => $type_fr,
          'name'                 => $name,
          'url'                  => $url,
          'entity'               => $entity,
          'entity_url'           => $entity_url,
          'serial_number'        => $serial_number,
          'inventaire_number'    => $inventaire_number,
        ];
      }
    }

    // tri ordre alpha
    array_multisort(array_column($myItems, 'name'), SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $myItems);
    array_multisort(array_column($myItems, 'type'), SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $myItems);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('items', $myItems);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('serial_number', $translator->translate('Serial number'));
    $viewData->addTranslation('inventaire_number', $translator->translate('Inventory number'));

    return $view->render($response, 'subitem/items.html.twig', (array)$viewData);
  }

  public function showSubProjecttasks(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\Project();
    $myItem2 = $item2->get();

    $rootUrl = $this->genereRootUrl($request, '/projecttasks');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myProjecttasks = [];
    $tabProjecttasksId = [];
    foreach ($myItem->projecttasks as $projecttask)
    {
      $projecttask_id = $projecttask->projecttask_id;
      $tabProjecttasksId[] = $projecttask_id;
    }

    foreach ($myItem2 as $current_item)
    {
      if ($current_item->tasks !== null)
      {
        foreach ($current_item->tasks as $task)
        {
          if (in_array($task->id, $tabProjecttasksId))
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

            $project = $current_item->name;
            $project_url = $this->genereRootUrl2Link($rootUrl2, '/projects/', $current_item->id);

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
              'project'               => $project,
              'project_url'           => $project_url,
            ];
          }
        }
      }
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
    $viewData->addTranslation('projects', $translator->translatePlural('Type', 'Types', 2));
    $viewData->addTranslation('projecttasks', $translator->translatePlural('Project task', 'Project tasks', 2));

    return $view->render($response, 'subitem/projecttasks.html.twig', (array)$viewData);
  }
}
