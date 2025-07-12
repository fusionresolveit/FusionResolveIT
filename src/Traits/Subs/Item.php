<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Item
{
  /**
   * @param array<string, string> $args
   */
  public function showSubItems(Request $request, Response $response, array $args): Response
  {
    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/items');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myItems = [];
    $models = $this->modelsForSubItem();
    foreach ($models as $func => $model)
    {
      $item = $this->instanciateModel();
      $myItem = $item->with($func)->where('id', $args['id'])->first();
      if (is_null($myItem))
      {
        continue;
      }
      foreach ($myItem->{$func} as $myItem3)
      {
        $type_fr = $myItem3->getTitle();
        $type = $myItem3->getTable();

        if (array_key_exists($type, $myItems) !== true)
        {
          $myItems[$type] = [
            'type'  => $type,
            'name'  => $type_fr,
            'items' => [],
          ];
        }

        $current_id = $myItem3->getAttribute('id');

        $name = $myItem3->getAttribute('name');
        if ($name == '')
        {
          $name = '(' . $current_id . ')';
        }

        $url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $current_id);

        $location = '';
        $location_url = '';
        if (property_exists($myItem3, 'location') && $myItem3->location !== null)
        {
          $location = $myItem3->location->name;
          $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $myItem3->location->id);
        }

        $documents = [];
        if (property_exists($myItem3, 'documents') && $myItem3->documents !== null)
        {
          foreach ($myItem3->documents as $document)
          {
            $url_document = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

            $documents[$document->id] = [
              'name'  => $document->name,
              'url'   => $url_document,
            ];
          }
        }

        $myItems[$type]['items'][$current_id][$myItem3->id] = [
          'name'            => $name,
          'url'             => $url,
          'location'        => $location,
          'location_url'    => $location_url,
          'documents'       => $documents,
        ];
      }
    }

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    // tri ordre alpha
    array_multisort(array_column($myItems, 'name'), SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $myItems);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('items', $myItems);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('name', pgettext('global', 'Name'));
    $viewData->addTranslation('location', npgettext('global', 'Location', 'Locations', 2));
    $viewData->addTranslation('documents', npgettext('global', 'Document', 'Documents', 2));

    return $view->render($response, 'subitem/items.html.twig', (array)$viewData);
  }
}
