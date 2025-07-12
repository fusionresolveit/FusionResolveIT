<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Problem
{
  /**
   * @param array<string, string> $args
   */
  public function showSubProblems(Request $request, Response $response, array $args): Response
  {
    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $item2 = new \App\Models\Problem();
    $myItem2 = $item2::with('requester', 'requestergroup', 'technician', 'techniciangroup')->get();

    $rootUrl = $this->genereRootUrl($request, '/problems');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $problems = [];
    foreach ($myItem2 as $problem)
    {
      $add_to_tab = false;

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
          if ($this->choose == 'users')
          {
            if ($requester->id == $args['id'])
            {
              $add_to_tab = true;
            }
          }
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $requester->id);

          $requesters[] = [
            'url'   => $requester_url,
            'name'  => $this->genereUserName($requester->name, $requester->lastname, $requester->firstname),
          ];
        }
      }
      if ($problem->requestergroup !== null)
      {
        foreach ($problem->requestergroup as $requestergroup)
        {
          if ($this->choose == 'groups')
          {
            if ($requestergroup->id == $args['id'])
            {
              $add_to_tab = true;
            }
          }
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $requestergroup->id);

          $requesters[] = [
            'url'   => $requester_url,
            'name'  => $requestergroup->completename,
          ];
        }
      }

      $technicians = [];
      if ($problem->technician !== null)
      {
        foreach ($problem->technician as $technician)
        {
          if ($this->choose == 'users')
          {
            if ($technician->id == $args['id'])
            {
              $add_to_tab = true;
            }
          }
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $technician->id);

          $technicians[] = [
            'url'   => $technician_url,
            'name'  => $this->genereUserName($technician->name, $technician->lastname, $technician->firstname),
          ];
        }
      }
      if ($problem->techniciangroup !== null)
      {
        foreach ($problem->techniciangroup as $techniciangroup)
        {
          if ($this->choose == 'groups')
          {
            if ($techniciangroup->id == $args['id'])
            {
              $add_to_tab = true;
            }
          }
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $techniciangroup->id);

          $technicians[] = [
            'url'   => $technician_url,
            'name'  => $techniciangroup->completename,
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

      if ($add_to_tab)
      {
        $problems[$problem->id] = [
          'url'                 => $url,
          'status'              => $status,
          'date'                => $problem->created_at,
          'last_update'         => $problem->updated_at,
          'entity'              => $entity,
          'entity_url'          => $entity_url,
          'priority'            => $priority,
          'requesters'          => $requesters,
          'technicians'         => $technicians,
          'title'               => $problem->name,
          'category'            => $category,
          'category_url'        => $category_url,
          'planification'       => $planification,
        ];
      }
    }

    // tri de la + récente à la + ancienne
    array_multisort(array_column($problems, 'last_update'), SORT_DESC, SORT_NATURAL | SORT_FLAG_CASE, $problems);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('problems', $problems);

    $viewData->addTranslation('problems', npgettext('problem', 'Problem', 'Problems', 2));
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
    $viewData->addTranslation('no_problem_found', pgettext('problem', 'No problem found.'));

    return $view->render($response, 'subitem/itilproblems.html.twig', (array)$viewData);
  }
}
