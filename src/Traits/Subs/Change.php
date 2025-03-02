<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Change
{
  /**
   * @param array<string, string> $args
   */
  public function showSubChanges(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item->with(['changes'])->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/changes');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $changes = [];
    foreach ($myItem->changes as $change)
    {
      $add_to_tab = false;
      if ($this->choose == 'tickets')
      {
        if ($change->getRelationValue('pivot')->ticket_id == $args['id'])
        {
          $add_to_tab = true;
        }
      }
      if ($this->choose == 'problems')
       {
        if ($change->getRelationValue('pivot')->problem_id == $args['id'])
        {
          $add_to_tab = true;
        }
      }

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
            'url'   => $requester_url,
            'name'  => $this->genereUserName($requester->name, $requester->lastname, $requester->firstname),
          ];
        }
      }
      if ($change->requestergroup !== null)
      {
        foreach ($change->requestergroup as $requestergroup)
        {
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $requestergroup->id);

          $requesters[] = [
            'url'   => $requester_url,
            'name'  => $requestergroup->completename,
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
            'url'   => $technician_url,
            'name'  => $this->genereUserName($technician->name, $technician->lastname, $technician->firstname),
          ];
        }
      }
      if ($change->techniciangroup !== null)
      {
        foreach ($change->techniciangroup as $techniciangroup)
        {
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $techniciangroup->id);

          $technicians[] = [
            'url'   => $technician_url,
            'name'  => $techniciangroup->completename,
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

      if ($add_to_tab)
      {
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
    }

    // tri de la + récente à la + ancienne
    array_multisort(array_column($changes, 'last_update'), SORT_DESC, SORT_NATURAL | SORT_FLAG_CASE, $changes);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('changes', $changes);

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
    $viewData->addTranslation('no_change_found', $translator->translate('No change found.'));

    return $view->render($response, 'subitem/itilchanges.html.twig', (array)$viewData);
  }
}
