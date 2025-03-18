<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Associateditem
{
  /**
   * @param array<string, string> $args
   */
  public function showSubAssociatedItems(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $myAssociatedItems = [];
    $models = $this->modelsForSubAssociateditem();
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
        $type = $model->getTable();
        $type_fr = $model->getTitle();

        $name = $myItem3->name;
        if ($name == '')
        {
          $name = '(' . $myItem3->id . ')';
        }

        // if (substr($type, 0, 6) == 'device')
        // {
        //   $url = $this->genereRootUrl2Link($rootUrl2, '/devices/' . $type . '/', $myItem3->id);
        // } else {
        //   $url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $myItem3->id);
        // }

        $entity = '';
        $entity_url = '';
        if ($myItem3->getAttribute('entity') !== null)
        {
          $entity = $myItem3->getAttribute('entity')->completename;
          // $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $myItem3->entity->id);
        }

        $serial_number = $myItem3->getAttribute('serial');

        $inventaire_number = $myItem3->getAttribute('otherserial');

        $myAssociatedItems[] = [
          'type'                 => $type_fr,
          'name'                 => $name,
          // 'url'                  => $url,
          'entity'               => $entity,
          'entity_url'           => $entity_url,
          'serial_number'        => $serial_number,
          'inventaire_number'    => $inventaire_number,
        ];
      }
    }

    // tri ordre alpha
    array_multisort(
      array_column($myAssociatedItems, 'name'),
      SORT_ASC,
      SORT_NATURAL | SORT_FLAG_CASE,
      $myAssociatedItems
    );
    array_multisort(
      array_column($myAssociatedItems, 'type'),
      SORT_ASC,
      SORT_NATURAL | SORT_FLAG_CASE,
      $myAssociatedItems
    );
    $item = $this->instanciateModel();
    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    // $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('associateditems', $myAssociatedItems);
    $viewData->addData('show', 'computer');

    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('serial_number', $translator->translate('Serial number'));
    $viewData->addTranslation('inventaire_number', $translator->translate('Inventory number'));

    return $view->render($response, 'subitem/associateditems.html.twig', (array)$viewData);
  }
}
