<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Appliance
{
  /**
   * @param array<string, string> $args
   */
  public function showSubAppliances(Request $request, Response $response, array $args): Response
  {
    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('appliances')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/appliances');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myAppliances = [];
    foreach ($myItem->appliances as $appliance)
    {
      $appliance_url = $this->genereRootUrl2Link($rootUrl2, '/appliances/', $appliance->id);

      $myAppliances[] = [
        'name'  => $appliance->name,
        'url'   => $appliance_url,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('appliances', $myAppliances);

    $viewData->addTranslation('name', pgettext('global', 'Name'));

    return $view->render($response, 'subitem/appliances.html.twig', (array)$viewData);
  }
}
