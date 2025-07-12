<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use App\DataInterface\PostTicket;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\Subs\Approval;
use App\Traits\Subs\Change;
use App\Traits\Subs\Cost;
use App\Traits\Subs\History;
use App\Traits\Subs\Item;
use App\Traits\Subs\Knowledgebasearticle;
use App\Traits\Subs\Project;

final class Ticket extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowAll;

  // Sub
  use Knowledgebasearticle;
  use History;
  use Cost;
  use Approval;
  use Change;
  use Project;
  use Item;

  protected $model = \App\Models\Ticket::class;
  protected $rootUrl2 = '/tickets/';
  protected $choose = 'tickets';

  protected function instanciateModel(): \App\Models\Ticket
  {
    return new \App\Models\Ticket();
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
   *          'itemSoftwarelicenses': \App\Models\Softwarelicense,
   *          'itemCertificates': \App\Models\Certificate,
   *          'itemLines': \App\Models\Line,
   *          'itemDcrooms': \App\Models\Dcroom,
   *          'itemRacks': \App\Models\Rack,
   *          'itemEnclosures': \App\Models\Enclosure,
   *          'itemClusters': \App\Models\Cluster,
   *          'itemPdus': \App\Models\Pdu,
   *          'itemDomains': \App\Models\Domain,
   *          'itemDomainrecords': \App\Models\Domainrecord,
   *          'itemAppliances': \App\Models\Appliance,
   *          'itemPassivedcequipments': \App\Models\Passivedcequipment
   *         }
   */
  public function modelsForSubItem()
  {
    return [
      'itemComputers'           => new \App\Models\Computer(),
      'itemMonitors'            => new \App\Models\Monitor(),
      'itemNetworkequipments'   => new \App\Models\Networkequipment(),
      'itemPeripherals'         => new \App\Models\Peripheral(),
      'itemPhones'              => new \App\Models\Phone(),
      'itemPrinters'            => new \App\Models\Printer(),
      'itemSoftwares'           => new \App\Models\Software(),
      'itemSoftwarelicenses'    => new \App\Models\Softwarelicense(),
      'itemCertificates'        => new \App\Models\Certificate(),
      'itemLines'               => new \App\Models\Line(),
      'itemDcrooms'             => new \App\Models\Dcroom(),
      'itemRacks'               => new \App\Models\Rack(),
      'itemEnclosures'          => new \App\Models\Enclosure(),
      'itemClusters'            => new \App\Models\Cluster(),
      'itemPdus'                => new \App\Models\Pdu(),
      'itemDomains'             => new \App\Models\Domain(),
      'itemDomainrecords'       => new \App\Models\Domainrecord(),
      'itemAppliances'          => new \App\Models\Appliance(),
      'itemPassivedcequipments' => new \App\Models\Passivedcequipment(),
    ];
  }

  /**
   * @param array<string, string> $args
   */
  public function showItem(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Ticket();
    return $this->commonShowITILItem($request, $response, $args, $item);
  }

  /**
   * @param array<string, string> $args
   */
  public function showNewItem(Request $request, Response $response, array $args): Response
  {
    $session = new \SlimSession\Helper();
    $session['ticketCreationDate'] = gmdate('Y-m-d H:i:s');

    $item = new \App\Models\Ticket();
    return $this->commonShowITILNewItem($request, $response, $args, $item);
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostTicket((object) $request->getParsedBody());

    $data = $this->prepareDataSave($data);

    $ticket = new \App\Models\Ticket();

    // manage rules
    $data = $this->runRules($data);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($ticket))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $dataCreate = $data->exportToArray();
    $ticket = \App\Models\Ticket::create($dataCreate);

    $this->updateRelationshipsMany($dataCreate, 'requester', $ticket, 1);
    $this->updateRelationshipsMany($dataCreate, 'requestergroup', $ticket, 1);
    $this->updateRelationshipsMany($dataCreate, 'watcher', $ticket, 3);
    $this->updateRelationshipsMany($dataCreate, 'watchergroup', $ticket, 3);
    $this->updateRelationshipsMany($dataCreate, 'technician', $ticket, 2);
    $this->updateRelationshipsMany($dataCreate, 'techniciangroup', $ticket, 2);

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($ticket, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/tickets/' . $ticket->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/tickets')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostTicket((object) $request->getParsedBody());
    $id = intval($args['id']);

    $data = $this->prepareDataSave($data, $id);

    // manage rules
    $data = $this->runRules($data, $id);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $ticket = \App\Models\Ticket::where('id', $id)->first();
    if (is_null($ticket))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($ticket))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $dataUpdate = $data->exportToArray();
    $ticket->update($dataUpdate);

    $this->updateRelationshipsMany($dataUpdate, 'requester', $ticket, 1);
    $this->updateRelationshipsMany($dataUpdate, 'requestergroup', $ticket, 1);
    $this->updateRelationshipsMany($dataUpdate, 'watcher', $ticket, 3);
    $this->updateRelationshipsMany($dataUpdate, 'watchergroup', $ticket, 3);
    $this->updateRelationshipsMany($dataUpdate, 'technician', $ticket, 2);
    $this->updateRelationshipsMany($dataUpdate, 'techniciangroup', $ticket, 2);


    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($ticket, 'update');

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
    $ticket = \App\Models\Ticket::withTrashed()->where('id', $id)->first();
    if (is_null($ticket))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($ticket->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $ticket->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/tickets')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $ticket->delete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('softdeleted');
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
    $ticket = \App\Models\Ticket::withTrashed()->where('id', $id)->first();
    if (is_null($ticket))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($ticket->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $ticket->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array{name?: string, content?: string, entity?: \App\Models\Entity, type?: int, status?: int,
   *              category?: \App\Models\Category, location?: \App\Models\Location, urgency?: int, impact?: int,
   *              priority?: int, time_to_resolve?: int, usersidlastupdater?: \App\Models\User,
   *              usersidrecipient?: \App\Models\User, requester?: array<\App\Models\User>,
   *              requestergroup?: array<\App\Models\Group>, watcher?: array<\App\Models\User>,
   *              watchergroup?: array<\App\Models\Group>, technician?: array<\App\Models\User>,
   *              techniciangroup?: array<\App\Models\Group>} $dataUpdate
   * @param 'requester'|'requestergroup'|'watcher'|'watchergroup'|'technician'|'techniciangroup' $relationship
   */
  public static function updateRelationshipsMany(
    array $dataUpdate,
    string $relationship,
    \App\Models\Ticket $ticket,
    int $type
  ): void
  {
    if (isset($dataUpdate[$relationship]))
    {
      $dbItems = [];
      foreach ($ticket->{$relationship} as $relationItem)
      {
        $dbItems[] = $relationItem->id;
      }

      // To delete
      $toDelete = array_diff($dbItems, $dataUpdate[$relationship]);
      foreach ($toDelete as $groupId)
      {
        $ticket->{$relationship}()->detach($groupId);
      }

      // To add
      $toAdd = array_diff($dataUpdate[$relationship], $dbItems);
      foreach ($toAdd as $groupId)
      {
        $ticket->{$relationship}()->attach($groupId, ['type' => $type]);
      }
    }
  }

  public function runRules(PostTicket $data, int|null $id = null): PostTicket
  {
    // Run ticket rules
    $rule = new \App\v1\Controllers\Rules\Ticket();
    if (is_null($id))
    {
      $ticket = new \App\Models\Ticket();
    } else {
      $ticket = \App\Models\Ticket::where('id', $id)->first();
      if (is_null($ticket))
      {
        throw new \Exception('Id not found', 404);
      }
    }

    $data = $rule->prepareData($ticket, $data);
    return $rule->processAllRules($data);
  }

  /**
   * @param array<string, string> $args
   */
  public function showStats(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Ticket();
    $view = Twig::fromRequest($request);

    $myItem = $item::where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/stats');

    $feeds = [];

    $feeds[] = [
      'date'  => $myItem->created_at,
      'text'  => pgettext('ITIL', 'Opening date'),
      'icon'  => 'pencil alternate',
      'color' => 'blue'
    ];

    $feeds[] = [
      'date'  => $myItem->time_to_resolve,
      'text'  => pgettext('ITIL', 'Time to resolve'),
      'icon'  => 'hourglass half',
      'color' => 'blue'
    ];
    if ($myItem->status >= 5)
    {
      $feeds[] = [
        'date'  => $myItem->solved_at,
        'text'  => pgettext('ITIL', 'Resolution date'),
        'icon'  => 'check circle',
        'color' => 'blue'
      ];
    }
    if ($myItem->status == 6)
    {
      $feeds[] = [
        'date'  => $myItem->closed_at,
        'text'  => pgettext('ITIL', 'Closing date'),
        'icon'  => 'flag checkered',
        'color' => 'blue'
      ];
    }


    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('feeds', $feeds);

    return $view->render($response, 'subitem/stats.html.twig', (array) $viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showProblem(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Ticket();
    $view = Twig::fromRequest($request);

    $myItem = $item::with(['problems'])->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

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
    $viewData->addData('feeds', $item->getFeeds(intval($args['id'])));
    $content = null;
    if (!is_null($myItem->content))
    {
      $viewData->addData('content', \App\v1\Controllers\Toolbox::convertMarkdownToHtml($myItem->content));
    }
    $viewData->addData('problems', $problems);
    $viewData->addData('csrf', \App\v1\Controllers\Toolbox::generateCSRF($request));

    $viewData->addTranslation('attachItem', pgettext('problem', 'Attach to an existant problem'));
    $viewData->addTranslation('selectItem', pgettext('problem', 'Select problem...'));
    $viewData->addTranslation('buttonAttach', pgettext('button', 'Attach'));
    $viewData->addTranslation('addItem', pgettext('problem', 'Add new problem'));
    $viewData->addTranslation('buttonCreate', pgettext('button', 'Create'));
    $viewData->addTranslation('attachedItems', pgettext('problem', 'Problems attached'));
    $viewData->addTranslation('updated', pgettext('global', 'Last update'));
    $viewData->addTranslation('or', pgettext('global', 'Or'));

    return $view->render($response, 'subitem/problem.html.twig', (array) $viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function postProblem(Request $request, Response $response, array $args): Response
  {
    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'problem') && is_numeric($data->problem))
    {
      $myItem = \App\Models\Ticket::where('id', $args['id'])->first();
      if (is_null($myItem))
      {
        throw new \Exception('Id not found', 404);
      }
      $myItem->problems()->attach((int)$data->problem);

      // add message to session
      \App\v1\Controllers\Toolbox::addSessionMessage(
        pgettext('session message', 'The ticket has been attached to problem successfully')
      );
    }
    else
    {
      // add message to session
      \App\v1\Controllers\Toolbox::addSessionMessage(
        pgettext('session message', 'Error to attach ticket to problem'),
        'error'
      );
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
  public static function computePriority(int $urgency, int $impact): int
  {
    $priority_matrix = \App\Models\Config::where('context', 'core')->where('name', 'priority_matrix')->first();
    if (!is_null($priority_matrix) && !is_null($priority_matrix->value))
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
  public function prepareDataSave(PostTicket $data, int|null $id = null): PostTicket
  {
    if (is_null($id))
    {
      $myItem = new \App\Models\Ticket();
    }
    else
    {
      $myItem = \App\Models\Ticket::where('id', $id)->first();
      if (is_null($myItem))
      {
        throw new \Exception('Id not found', 404);
      }
    }

    if (is_null($data->impact))
    {
      $data->impact = 3;
    }

    if (is_null($data->urgency))
    {
      $data->urgency = 3;
    }

    if ($myItem->priority == $data->priority)
    {
      $data->priority = self::computePriority($data->urgency, $data->impact);
    }

    // TODO rules
    return $data;
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubProjecttasks(Request $request, Response $response, array $args): Response
  {
    $view = Twig::fromRequest($request);

    $ticket = \App\Models\Ticket::where('id', $args['id'])->with('projecttasks')->first();
    if (is_null($ticket))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/projecttasks');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myProjecttasks = [];
    foreach ($ticket->projecttasks as $task)
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

      $project = $task->project;
      $project_url = null;
      if (!is_null($project))
      {
        $project_url = $this->genereRootUrl2Link($rootUrl2, '/projects/', $project->id);
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
        'project'               => $project,
        'project_url'           => $project_url,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($ticket, $request);
    $viewData->addRelatedPages($ticket->getRelatedPages($rootUrl));

    $viewData->addData('fields', $ticket->getFormData($ticket));
    $viewData->addData('projecttasks', $myProjecttasks);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('name', pgettext('global', 'Name'));
    $viewData->addTranslation('type', npgettext('global', 'Type', 'Types', 1));
    $viewData->addTranslation('status', pgettext('global', 'Status'));
    $viewData->addTranslation('percent_done', pgettext('global', 'Percent done'));
    $viewData->addTranslation('planned_start_date', pgettext('ITIL', 'Planned start date'));
    $viewData->addTranslation('planned_end_date', pgettext('ITIL', 'Planned end date'));
    $viewData->addTranslation('planned_duration', pgettext('ITIL', 'Planned duration'));
    $viewData->addTranslation('effective_duration', pgettext('project', 'Effective duration'));
    $viewData->addTranslation('father', pgettext('global', 'Father'));
    $viewData->addTranslation('projects', npgettext('global', 'Type', 'Types', 2));
    $viewData->addTranslation('projecttasks', npgettext('project', 'Project task', 'Project tasks', 2));

    return $view->render($response, 'subitem/projecttasks.html.twig', (array)$viewData);
  }
}
