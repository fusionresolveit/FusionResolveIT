<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostProjecttask;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Note;
use App\Traits\Subs\Ticket;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Projecttask extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Note;
  use Document;
  use History;
  use Ticket;

  protected $model = \App\Models\Projecttask::class;
  protected $rootUrl2 = '/projecttasks/';
  protected $choose = 'projecttasks';

  protected function instanciateModel(): \App\Models\Projecttask
  {
    return new \App\Models\Projecttask();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostProjecttask((object) $request->getParsedBody());

    $projecttask = new \App\Models\Projecttask();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($projecttask))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $projecttask = \App\Models\Projecttask::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($projecttask, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/projecttasks/' . $projecttask->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/projecttasks')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostProjecttask((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $projecttask = \App\Models\Projecttask::where('id', $id)->first();
    if (is_null($projecttask))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($projecttask))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $projecttask->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($projecttask, 'update');

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
    $projecttask = \App\Models\Projecttask::withTrashed()->where('id', $id)->first();
    if (is_null($projecttask))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($projecttask->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $projecttask->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/projecttasks')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $projecttask->delete();
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
    $projecttask = \App\Models\Projecttask::withTrashed()->where('id', $id)->first();
    if (is_null($projecttask))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($projecttask->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $projecttask->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
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
    $item = new \App\Models\Projecttask();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $item2 = new \App\Models\Projecttask();
    $myItem2 = $item2::where('projecttask_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/projecttasks');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myProjecttasks = [];
    foreach ($myItem2 as $current_task)
    {
      $name = $current_task->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/projecttasks/', $current_task->id);

      $type = '';
      $type_url = '';
      if ($current_task->type !== null)
      {
        $type = $current_task->type->name;
        $type_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/projecttasktypes/', $current_task->type->id);
      }

      $status = '';
      $status_color = '';
      if ($current_task->state !== null)
      {
        $status = $current_task->state->name;
        $status_color = $current_task->state->color;
      }

      $percent_done = $current_task->percent_done;
      $percent_done = $this->getValueWithUnit($percent_done, '%', 0);

      $planned_start_date = $current_task->plan_start_date;

      $planned_end_date = $current_task->plan_end_date;

      $planned_duration = $this->timestampToString($current_task->planned_duration, false);

      $effective_duration = $this->timestampToString($current_task->effective_duration, false);

      $father = '';
      $father_url = '';
      if ($current_task->parent !== null)
      {
        $father = $current_task->parent->name;
        $father_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/projecttasks/', $current_task->parent->id);
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

    $viewData->addTranslation('name', pgettext('global', 'Name'));
    $viewData->addTranslation('type', npgettext('global', 'Type', 'Types', 1));
    $viewData->addTranslation('status', pgettext('global', 'Status'));
    $viewData->addTranslation('percent_done', pgettext('global', 'Percent done'));
    $viewData->addTranslation('planned_start_date', pgettext('ITIL', 'Planned start date'));
    $viewData->addTranslation('planned_end_date', pgettext('ITIL', 'Planned end date'));
    $viewData->addTranslation('planned_duration', pgettext('ITIL', 'Planned duration'));
    $viewData->addTranslation('effective_duration', pgettext('project', 'Effective duration'));
    $viewData->addTranslation('father', pgettext('global', 'Father'));

    return $view->render($response, 'subitem/projecttasks.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubProjecttaskteams(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Projecttask();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $item2 = new \App\Models\Projecttaskteam();
    $myItem2 = $item2::where('projecttask_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/projecttaskteams');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myProjecttaskteams = [];
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

        $myProjecttaskteams[] = [
          'member'   => $name,
          'url'      => $url,
          'type'     => $type_fr,
        ];
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('projecttaskteams', $myProjecttaskteams);

    $viewData->addTranslation('type', npgettext('global', 'Type', 'Types', 1));
    $viewData->addTranslation('member', npgettext('ITIL', 'Member', 'Members', 2));

    return $view->render($response, 'subitem/projecttaskteams.html.twig', (array)$viewData);
  }
}
