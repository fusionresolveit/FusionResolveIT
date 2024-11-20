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
      $planned_duration = $task->planned_duration;
      $effective_duration = $task->effective_duration;
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

    $myItem = $item::with('parent_of')->find($args['id']);

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/parent_of');
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myProjects = [];
    foreach ($myItem->parent_of as $parent_of)
    {
      $name = $parent_of->name;

      $url = '';
      if ($rootUrl2 != '') {
        $url = $rootUrl2 . "/projects/" . $parent_of->id;
      }

      $status = '';
      $status_color = '';
      if ($parent_of->state !== null)
      {
        $status = $parent_of->state->name;
        $status_color = $parent_of->state->color;
      }
      $open_date = $parent_of->date;
      $last_update = $parent_of->updated_at;
      $entity = '';
      if ($parent_of->entity !== null)
      {
        $entity = $parent_of->entity->name;
      }
      $priority = $this->getPriorityArray()[$parent_of->priority];

      $manager = '';
      $manager_url = '';
      if ($parent_of->user !== null)
      {
        $manager = $parent_of->user->name;
        if ($rootUrl2 != '') {
          $manager_url = $rootUrl2 . "/users/" . $parent_of->user->id;
        }
      }
      $manager_group = '';
      $manager_group_url = '';
      if ($parent_of->group !== null)
      {
        $manager_group = $parent_of->group->name;
        if ($rootUrl2 != '') {
          $manager_group_url = $rootUrl2 . "/groups/" . $parent_of->group->id;
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

  public static function getPriorityArray()
  {
    global $translator;
    return [
      6 => [
        'title' => $translator->translate('priority' . "\004" . 'Major'),
        'color' => 'gsitmajor',
        'icon'  => 'fire extinguisher',
      ],
      5 => [
        'title' => $translator->translate('priority' . "\004" . 'Very high'),
        'color' => 'gsitveryhigh',
        'icon'  => 'fire alternate',
      ],
      4 => [
        'title' => $translator->translate('priority' . "\004" . 'High'),
        'color' => 'gsithigh',
        'icon'  => 'fire',
      ],
      3 => [
        'title' => $translator->translate('priority' . "\004" . 'Medium'),
        'color' => 'gsitmedium',
        'icon'  => 'volume up',
      ],
      2 => [
        'title' => $translator->translate('priority' . "\004" . 'Low'),
        'color' => 'gsitlow',
        'icon'  => 'volume down',
      ],
      1 => [
        'title' => $translator->translate('priority' . "\004" . 'Very low'),
        'color' => 'gsitverylow',
        'icon'  => 'volume off',
      ],
    ];
  }
}
