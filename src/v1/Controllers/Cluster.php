<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Cluster extends Common
{
  protected $model = '\App\Models\Cluster';
  protected $rootUrl2 = '/clusters/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Cluster();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Cluster();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Cluster();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubItems(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\Clusteritem();
    $myItem2 = $item2::where('cluster_id', $args['id'])->get();

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/items');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myItems = [];
    foreach ($myItem2 as $item)
    {
      $item3 = new $item->item_type();
      $myItem3 = $item3->find($item->item_id);

      if ($myItem3 != null) {
        $name = $myItem3->name;
        if ($name == '') {
          $name = '(' . $myItem3->id . ')';
        }

        $url = '';
        if ($rootUrl2 != '') {
          $table = $item3->getTable();
          if ($table != '') {
            $url = $rootUrl2 . "/" . $table . "/" . $myItem3->id;
          }
        }

        $myItems[] = [
          'name'     => $name,
          'url'      => $url,
        ];
      }
    }

    // tri ordre alpha
    usort($myItems, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('items', $myItems);
    $viewData->addData('show', 'cluster');

    $viewData->addTranslation('name', $translator->translatePlural('Item', 'Items', 1));

    return $view->render($response, 'subitem/items.html.twig', (array)$viewData);
  }
}
