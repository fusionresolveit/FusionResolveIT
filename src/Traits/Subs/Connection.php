<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Connection
{
  /**
   * @param array<string, string> $args
   */
  public function showSubConnections(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('connections')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/connections');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myConnections = [];
    foreach ($myItem->connections as $connection)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/computers/', $connection->id);

      $entity = '';
      $entity_url = '';
      if ($connection->entity !== null)
      {
        $entity = $connection->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $connection->entity->id);
      }

      if ($connection->getRelationValue('pivot')->is_dynamic == 1)
      {
        $auto_val = $translator->translate('Yes');
      } else {
        $auto_val = $translator->translate('No');
      }

      $myConnections[] = [
        'name'                 => $connection->name,
        'url'                  => $url,
        'auto'                 => $connection->getRelationValue('pivot')->is_dynamic,
        'auto_val'             => $auto_val,
        'entity'               => $entity,
        'entity_url'           => $entity_url,
        'serial_number'        => $connection->serial,
        'inventaire_number'    => $connection->otherserial,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('connections', $myConnections);
    $viewData->addData('show', 'default');

    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('auto', $translator->translate('Automatic inventory'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('serial_number', $translator->translate('Serial number'));
    $viewData->addTranslation('inventaire_number', $translator->translate('Inventory number'));
    $viewData->addTranslation('no_connection_found', $translator->translate('Not connected.'));

    return $view->render($response, 'subitem/connections.html.twig', (array)$viewData);
  }
}
