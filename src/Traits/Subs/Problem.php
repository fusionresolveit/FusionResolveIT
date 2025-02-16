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
    global $translator;

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

    $viewData->addTranslation('problems', $translator->translatePlural('Problem', 'Problems', 2));
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
    $viewData->addTranslation('no_problem_found', $translator->translate('No problem found.'));

    return $view->render($response, 'subitem/itilproblems.html.twig', (array)$viewData);
  }
}
