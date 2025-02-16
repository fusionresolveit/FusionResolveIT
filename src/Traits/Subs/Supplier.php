<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Supplier
{
  /**
   * @param array<string, string> $args
   */
  public function showSubSuppliers(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('suppliers')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/suppliers');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $mySuppliers = [];
    foreach ($myItem->suppliers as $supplier)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/suppliers/', $supplier->id);

      $entity = '';
      $entity_url = '';
      if ($supplier->entity !== null)
      {
        $entity = $supplier->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $supplier->entity->id);
      }

      $type = '';
      $type_url = '';
      if ($supplier->type !== null)
      {
        $type = $supplier->type->name;
        $type_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/suppliertypes/', $supplier->type->id);
      }

      $mySuppliers[$supplier->id] = [
        'name'           => $supplier->name,
        'url'            => $url,
        'entity'         => $entity,
        'entity_url'     => $entity_url,
        'type'           => $type,
        'type_url'       => $type_url,
        'phone'          => $supplier->phonenumber,
        'fax'            => $supplier->fax,
        'website'        => $supplier->website,
      ];
    }

    array_multisort(array_column($mySuppliers, 'name'), SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $mySuppliers);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('suppliers', $mySuppliers);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('type', $translator->translatePlural('Third party type', 'Third party types', 1));
    $viewData->addTranslation('phone', $translator->translatePlural('Phone', 'Phones', 1));
    $viewData->addTranslation('fax', $translator->translate('Fax'));
    $viewData->addTranslation('website', $translator->translate('Website'));

    return $view->render($response, 'subitem/suppliers.html.twig', (array)$viewData);
  }
}
