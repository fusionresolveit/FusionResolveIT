<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Domain extends Common
{
  protected $model = '\App\Models\Domain';
  protected $rootUrl2 = '/domains/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Domain();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Domain();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Domain();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubRecords(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('records')->find($args['id']);

    $myRecords = [];
    foreach ($myItem->records as $record)
    {
      $type = '';
      if ($record->type !== null)
      {
        $type = $record->type->name;
      }

      $myRecords[] = [
        'name'     => $record->name,
        'type'     => $type,
        'ttl'      => $record->ttl,
        'target'   => $record->data,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/records');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('records', $myRecords);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('ttl', $translator->translate('TTL'));
    $viewData->addTranslation('target', $translator->translatePlural('Target', 'Targets', 1));

    return $view->render($response, 'subitem/records.html.twig', (array)$viewData);
  }
}
