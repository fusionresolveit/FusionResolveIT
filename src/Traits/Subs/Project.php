<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Project
{
  /**
   * @param array<string, string> $args
   */
  public function showSubProjects(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $itilItem = $item->where('id', $args['id'])->with('projects')->first();
    if (is_null($itilItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/projects');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myProjects = [];
    foreach ($itilItem->projects as $project)
    {
      $item3 = new \App\Models\Project();
      $myItem3 = $item3->where('id', $project->project_id)->first();
      if ($myItem3 !== null)
      {
        $name = $myItem3->name;

        $url = $this->genereRootUrl2Link($rootUrl2, '/projects/', $myItem3->id);

        $status = '';
        $status_color = '';
        if ($myItem3->state !== null)
        {
          $status = $myItem3->state->name;
          $status_color = $myItem3->state->color;
        }

        $open_date = $myItem3->created_at;

        $last_update = $myItem3->updated_at;

        $entity = '';
        $entity_url = '';
        if ($myItem3->entity !== null)
        {
          $entity = $myItem3->entity->completename;
          $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $myItem3->entity->id);
        }

        $priority = $this->getPriorityArray()[$myItem3->priority];

        $manager = '';
        $manager_url = '';
        if ($myItem3->user !== null)
        {
          $manager = $this->genereUserName($myItem3->user->name, $myItem3->user->lastname, $myItem3->user->firstname);
          $manager_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $myItem3->user->id);
        }

        $manager_group = '';
        $manager_group_url = '';
        if ($myItem3->group !== null)
        {
          $manager_group = $myItem3->group->completename;
          $manager_group_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $myItem3->group->id);
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
}
