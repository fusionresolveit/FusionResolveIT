<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Software
{
  /**
   * @param array<string, string> $args
   */
  public function showSubSoftwares(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('softwareversions')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/softwares');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $softwares = [];
    foreach ($myItem->softwareversions as $softwareversion)
    {
      if (is_null($softwareversion->software))
      {
        continue;
      }
      $softwareversion_url = $this->genereRootUrl2Link($rootUrl2, '/softwareversions/', $softwareversion->id);

      $software_url = $this->genereRootUrl2Link($rootUrl2, '/softwares/', $softwareversion->software->id);

      $softwares[] = [
        'id'        => $softwareversion->id,
        'name'      => $softwareversion->name,
        'url'       => $softwareversion_url,
        'software'  => [
          'id'    => $softwareversion->software->id,
          'name'  => $softwareversion->software->name,
          'url'   => $software_url,
        ]
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('softwares', $softwares);
    $viewData->addData('show', 'default');

    $viewData->addTranslation('software', $translator->translatePlural('Software', 'Software', 1));
    $viewData->addTranslation('version', $translator->translatePlural('Version', 'Versions', 1));

    return $view->render($response, 'subitem/softwares.html.twig', (array)$viewData);
  }
}
