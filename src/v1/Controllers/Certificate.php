<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Certificate extends Common
{
  protected $model = '\App\Models\Certificate';
  protected $rootUrl2 = '/certificates/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Certificate();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Certificate();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Certificate();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubDomains(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\Domain();
    $myItem2 = $item2::with('certificates')->get();

    $myDomains = [];
    foreach ($myItem2 as $domain)
    {
      $add_domain = false;
      if ($domain->certificates != null) {
        foreach ($domain->certificates as $certificate) {
          if ($args['id'] == $certificate->id) {
            $add_domain = true;
            break;
          }
        }
      }

      if ($add_domain) {
        $entity = '';
        if ($domain->entity !== null)
        {
          $entity = $domain->entity->name;
        }
        $groupstech = '';
        if ($domain->groupstech !== null)
        {
          $groupstech = $domain->groupstech->name;
        }
        $userstech = '';
        if ($domain->userstech !== null)
        {
          $userstech = $domain->userstech->name;
        }
        $type = '';
        if ($domain->type !== null)
        {
          $type = $domain->type->name;
        }

        $relation = '';
        $domainrelation = null;

        $item3 = new \App\Models\DomainItem();
        $myItem3 = $item3::with('relation')->where('domain_id', $domain->id)->get();

        foreach ($myItem3 as $domainitem) {
          if (($args['id'] == $domainitem->item_id) && ('\\' . $domainitem->item_type == $this->model))
          {
            $domainrelation = \App\Models\Domainrelation::find($domainitem->domainrelation_id);
            if ($domainrelation !== null)
            {
              $relation = $domainrelation->name;
            }
          }
        }

        $alert_expiration = false;
        $date_expiration = $domain->date_expiration;
        if ($date_expiration == null) {
          $date_expiration = $translator->translate("N'expire pas");
        } else {
          if ($date_expiration < date('Y-m-d H:i:s')) {
            $alert_expiration = true;
          }
        }

        $myDomains[] = [
          'name'              => $domain->name,
          'entity'            => $entity,
          'group'             => $groupstech,
          'user'              => $userstech,
          'type'              => $type,
          'relation'          => $relation,
          'date_create'       => $domain->created_at,
          'date_exp'          => $date_expiration,
          'alert_expiration'  => $alert_expiration,
        ];
      }
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/domains');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('domains', $myDomains);

    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('group', $translator->translate('Group in charge'));
    $viewData->addTranslation('user', $translator->translate('Technician in charge'));
    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('relation', $translator->translatePlural('Domain relation', 'Domains relations', 1));
    $viewData->addTranslation('date_create', $translator->translate('Creation date'));
    $viewData->addTranslation('date_exp', $translator->translate('Expiration date'));

    return $view->render($response, 'subitem/domains.html.twig', (array)$viewData);
  }
}
