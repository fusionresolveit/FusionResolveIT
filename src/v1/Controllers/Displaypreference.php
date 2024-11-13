<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

final class Displaypreference extends Common
{
  protected $model = '\App\Models\Displaypreference';

  public function manageColumnsOfModel(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getQueryParams();
    $view = Twig::fromRequest($request);

    if (!property_exists($data, 'm'))
    {
      throw new \Exception('Wrong request', 400);
    }
    $model = $data->m;
    if (!class_exists('\\' . $model))
    {
      throw new \Exception('Wrong request', 400);
    }

    $item = new $model();
    $definitions = $item->getDefinitions(true);
    $defIds = [];
    foreach ($definitions as $definition)
    {
      $defIds[$definition['id']] = $definition['title'];
    }

    $preferences = \App\Models\Displaypreference::
        where('itemtype', $model)
      ->where('user_id', $GLOBALS['user_id'])
      ->orderBy('rank', 'asc')->get();
    if (count($preferences) == 0)
    {
      $preferences = \App\Models\Displaypreference::
          where('itemtype', $model)
        ->where('user_id', 0)
        ->orderBy('rank', 'asc')->get();
    }
    $columns = [];
    foreach ($preferences as $pref)
    {
      if (!isset($defIds[$pref->num]))
      {
        continue;
      }
      $columns[] = [
        'prefid' => $pref->num,
        'title'  => $defIds[$pref->num],
        'id'     => $pref->num,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata(new \App\Models\Displaypreference(), $request);
    $viewData->addData('columns', $columns);
    $viewData->addData('definitions', $defIds);

    return $view->render($response, 'columns.html.twig', (array)$viewData);
  }
}
