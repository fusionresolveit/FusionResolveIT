<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait History
{
  /**
   * @param array<string, string> $args
   */
  public function showSubHistory(Request $request, Response $response, array $args): Response
  {
    $item = $this->instanciateModel();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    // Load the item
    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $logs = \App\Models\Log::where('item_type', ltrim($this->model, '\\'))
      ->where('item_id', $myItem->id)
      ->orderBy('id', 'desc')
      ->get();

    $fieldsTitle = [];
    foreach ($definitions as $def)
    {
      $fieldsTitle[$def->id] = $def->title;
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/history');

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($item, $request);

    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    // $viewData->addData('content', \App\v1\Controllers\Toolbox::convertMarkdownToHtml($myItem->content));
    $viewData->addData('history', $logs);
    $viewData->addData('titles', $fieldsTitle);

    $viewData->addTranslation('history', pgettext('history', 'History'));

    return $view->render($response, 'subitem/history.html.twig', (array)$viewData);
  }
}
