<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Contract extends Common
{
  protected $model = '\App\Models\Contract';
  protected $rootUrl2 = '/contracts/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Contract();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Contract();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Contract();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubCosts(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('costs')->find($args['id']);

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/costs');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myCosts = [];
    $total_cost = 0;
    foreach ($myItem->costs as $cost)
    {
      $budget = '';
      $budget_url = '';
      if ($cost->budget != null)
      {
        $budget = $cost->budget->name;

        if ($rootUrl2 != '') {
          $budget_url = $rootUrl2 . "/budgets/" . $cost->budget->id;
        }
      }

      $total_cost = $total_cost + ($cost->cost);

      $myCosts[$cost->id] = [
        'name'            => $cost->name,
        'begin_date'      => $cost->begin_date,
        'end_date'        => $cost->end_date,
        'budget'          => $budget,
        'budget_url'      => $budget_url,
        'cost'            => sprintf("%.2f",$cost->cost),
      ];
    }

    // tri de la + récente à la + ancienne
    usort($myCosts, function ($a, $b)
    {
      return strtolower($a['begin_date']) > strtolower($b['begin_date']);
    });


    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('costs', $myCosts);
    $viewData->addData('total_cost', sprintf("%.2f",$total_cost));

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('begin_date', $translator->translate('Start date'));
    $viewData->addTranslation('end_date', $translator->translate('End date'));
    $viewData->addTranslation('budget', $translator->translatePlural('Budget', 'Budgets', 1));
    $viewData->addTranslation('cost', $translator->translatePlural('Cost', 'Costs', 1));
    $viewData->addTranslation('total_cost', $translator->translate('Total cost'));

    return $view->render($response, 'subitem/costs.html.twig', (array)$viewData);
  }
}
