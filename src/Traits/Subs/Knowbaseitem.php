<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Knowbaseitem
{
  /**
   * @param array<string, string> $args
   */
  public function showSubKnowbaseitems(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('knowbaseitems')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/knowbaseitems');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myKnowbaseitems = [];
    foreach ($myItem->knowbaseitems as $knowbaseitem)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/knowbaseitems/', $knowbaseitem->id);

      $myKnowbaseitems[$knowbaseitem->id] = [
        'name'           => $knowbaseitem->name,
        'created_at'     => $knowbaseitem->created_at,
        'updated_at'     => $knowbaseitem->updated_at,
        'url'            => $url,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('knowbaseitems', $myKnowbaseitems);

    $viewData->addTranslation('name', $translator->translatePlural('Item', 'Items', 1));
    $viewData->addTranslation('created_at', $translator->translate('Creation date'));
    $viewData->addTranslation('updated_at', $translator->translate('Update date'));

    return $view->render($response, 'subitem/knowbaseitems.html.twig', (array)$viewData);
  }
}
