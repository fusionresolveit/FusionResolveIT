<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Domain
{
  /**
   * @param array<string, string> $args
   */
  public function showSubDomains(Request $request, Response $response, array $args): Response
  {
    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('domains')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/domains');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myDomains = [];
    foreach ($myItem->domains as $domain)
    {
      $entity = '';
      $entity_url = '';
      if ($domain->entity !== null)
      {
        $entity = $domain->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $domain->entity->id);
      }

      $grouptech = '';
      $grouptech_url = '';
      if ($domain->grouptech !== null)
      {
        $grouptech = $domain->grouptech->completename;
        $grouptech_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $domain->grouptech->id);
      }

      $usertech = '';
      $usertech_url = '';
      if ($domain->usertech !== null)
      {
        $usertech = $this->genereUserName(
          $domain->usertech->name,
          $domain->usertech->lastname,
          $domain->usertech->firstname
        );
        $usertech_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $domain->usertech->id);
      }

      $type = '';
      $type_url = '';
      if ($domain->type !== null)
      {
        $type = $domain->type->name;
        $type_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/domaintypes/', $domain->type->id);
      }

      $relation = '';
      $relation_url = '';
      $domainrelation = \App\Models\Domainrelation::
          where('id', $domain->getRelationValue('pivot')->domainrelation_id)
        ->first();
      if ($domainrelation !== null)
      {
        $relation = $domainrelation->name;
        $relation_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/domainrelations/', $domainrelation->id);
      }

      $alert_expiration = false;
      $date_expiration = $domain->date_expiration;
      if ($date_expiration == null)
      {
        $date_expiration = pgettext('management', 'Does not expire');
      } else {
        if ($date_expiration < date('Y-m-d H:i:s'))
        {
          $alert_expiration = true;
        }
      }

      $myDomains[] = [
        'name'              => $domain->name,
        'entity'            => $entity,
        'entity_url'        => $entity_url,
        'group'             => $grouptech,
        'group_url'         => $grouptech_url,
        'user'              => $usertech,
        'user_url'          => $usertech_url,
        'type'              => $type,
        'type_url'          => $type_url,
        'relation'          => $relation,
        'relation_url'      => $relation_url,
        'date_create'       => $domain->created_at,
        'date_exp'          => $date_expiration,
        'alert_expiration'  => $alert_expiration,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/domains');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('domains', $myDomains);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('entity', npgettext('global', 'Entity', 'Entities', 1));
    $viewData->addTranslation('group', pgettext('inventory device', 'Group in charge'));
    $viewData->addTranslation('user', pgettext('inventory device', 'Technician in charge'));
    $viewData->addTranslation('type', npgettext('global', 'Type', 'Types', 1));
    $viewData->addTranslation('relation', npgettext('global', 'Domain relation', 'Domain relations', 1));
    $viewData->addTranslation('date_create', pgettext('global', 'Creation date'));
    $viewData->addTranslation('date_exp', pgettext('global', 'Expiration date'));

    return $view->render($response, 'subitem/domains.html.twig', (array)$viewData);
  }
}
