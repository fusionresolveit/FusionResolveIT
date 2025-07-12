<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Certificate
{
  /**
   * @param array<string, string> $args
   */
  public function showSubCertificates(Request $request, Response $response, array $args): Response
  {
    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('certificates')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/certificates');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myCertificates = [];
    foreach ($myItem->certificates as $certificate)
    {
      $type = '';
      $type_url = '';
      if ($certificate->type !== null)
      {
        $type = $certificate->type->name;
        $type_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/certificatetypes/', $certificate->type->id);
      }

      $entity = '';
      $entity_url = '';
      if ($certificate->entity !== null)
      {
        $entity = $certificate->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $certificate->entity->id);
      }

      $alert_expiration = false;
      $date_expiration = $certificate->date_expiration;
      if ($date_expiration == null)
      {
        $date_expiration = pgettext('management', 'Does not expire');
      } else {
        if ($date_expiration < date('Y-m-d H:i:s'))
        {
          $alert_expiration = true;
        }
      }

      $state = '';
      $state_url = '';
      if ($certificate->state !== null)
      {
        $state = $certificate->state->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $certificate->state->id);
      }

      $myCertificates[] = [
        'name'              => $certificate->name,
        'entity'            => $entity,
        'entity_url'        => $entity_url,
        'type'              => $type,
        'type_url'          => $type_url,
        'dns_name'          => $certificate->dns_name,
        'dns_suffix'        => $certificate->dns_suffix,
        'created_at'        => $certificate->created_at,
        'date_expiration'   => $date_expiration,
        'alert_expiration'  => $alert_expiration,
        'state'             => $state,
        'state_url'         => $state_url,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('certificates', $myCertificates);

    $viewData->addTranslation('name', pgettext('global', 'Name'));
    $viewData->addTranslation('entity', npgettext('global', 'Entity', 'Entities', 1));
    $viewData->addTranslation('type', npgettext('global', 'Type', 'Types', 1));
    $viewData->addTranslation('dns_name', pgettext('certificate', 'DNS name'));
    $viewData->addTranslation('dns_suffix', pgettext('certificate', 'DNS suffix'));
    $viewData->addTranslation('created_at', pgettext('global', 'Creation date'));
    $viewData->addTranslation('date_expiration', pgettext('global', 'Expiration date'));
    $viewData->addTranslation('status', pgettext('inventory device', 'Status'));

    return $view->render($response, 'subitem/certificates.html.twig', (array)$viewData);
  }
}
