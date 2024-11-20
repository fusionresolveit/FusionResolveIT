<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Software extends Common
{
  protected $model = '\App\Models\Software';
  protected $rootUrl2 = '/softwares/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Software();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Software();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Software();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubVersions(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('versions')->find($args['id']);

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/versions');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myVersions = [];
    $total_install = 0;
    foreach ($myItem->versions as $version)
    {
      $url = '';
      if ($rootUrl2 != '') {
        $url = $rootUrl2 . "/softwareversion/" . $version->id;
      }
      $status = '';
      if ($version->state != null) {
        $status = $version->state->name;
      }
      $os = '';
      if ($version->operatingsystem != null) {
        $os = $version->operatingsystem->name;
      }
      $nb_install = 0;  // TODO

      $total_install = $total_install + $nb_install;

      $myVersions[] = [
        'name'          => $version->name,
        'url'           => $url,
        'status'        => $status,
        'os'            => $os,
        'nb_install'    => $nb_install,
        'comment'       => $version->comment,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('versions', $myVersions);
    $viewData->addData('total_install', $total_install);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('os', $translator->translate('Operating System'));
    $viewData->addTranslation('nb_install', $translator->translatePlural('Installation', 'Installations', 2));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));
    $viewData->addTranslation('no_item_found', $translator->translate('No items found.'));
    $viewData->addTranslation('total', $translator->translate('Total'));

    return $view->render($response, 'subitem/versions.html.twig', (array)$viewData);
  }
}
