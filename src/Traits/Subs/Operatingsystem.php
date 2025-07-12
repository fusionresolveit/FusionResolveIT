<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use App\DataInterface\PostItemOperatingsystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Operatingsystem
{
  /**
   * @param array<string, string> $args
   */
  public function showSubOperatingSystem(Request $request, Response $response, array $args): Response
  {
    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('operatingsystems')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/operatingsystem');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    foreach ($myItem->operatingsystems as $os)
    {
      // we got only the first item
      break;
    }
    $getDefs = $item->getSpecificFunction('getDefinitionOperatingSystem');

    $show = '';
    if ($this->rootUrl2 != '')
    {
      $show = str_ireplace('/', '', $this->rootUrl2);
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    if (isset($os))
    {
      $viewData->addData('fields', $item->getFormData($os, $getDefs));
    } else {
      $viewData->addData('fields', $item->getFormData(null, $getDefs));
    }
    $viewData->addData('show', $show);
    $viewData->addData('operatingsystem', []); // $operatingsystem);
    $viewData->addData('csrf', \App\v1\Controllers\Toolbox::generateCSRF($request));

    $viewData->addTranslation('entreprise', 'Entreprise');
    $viewData->addTranslation('oscomment', npgettext('global', 'Comment', 'Comments', 2));
    $viewData->addTranslation('hostid', 'HostID');
    $viewData->addTranslation('owner', 'Propriétaire');
    $viewData->addTranslation('install_date', pgettext('global', 'Installation date'));

    return $view->render($response, 'subitem/operatingsystems.html.twig', (array)$viewData);
  }

    /**
   * @param array<string, string> $args
   */
  public function saveSubOperatingSystem(Request $request, Response $response, array $args): Response
  {
    $data = new PostItemOperatingsystem((object) $request->getParsedBody(), $this->instanciateModel());

    $item = $this->instanciateModel();
    $myItem = $item::with('operatingsystems')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    if (!$this->canRightCreate())
    {
      // throw new \Exception('Unauthorized access', 401);
    }

    $dataSync = $data->exportToArray();
    if (isset($dataSync['operatingsystem_id']))
    {
      $myItem->operatingsystems()->sync([$dataSync['operatingsystem_id'] => $dataSync]);
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }
}
