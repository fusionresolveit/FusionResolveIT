<?php

declare(strict_types=1);

namespace App\Traits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait ShowNewItem
{
  /**
   * @param array<string, string> $args
   */
  public function showNewItem(Request $request, Response $response, array $args): Response
  {
    global $translator;
    $view = Twig::fromRequest($request);

    $item = $this->instanciateModel();

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($item, $request);

    $viewData->addData('fields', $item->getFormData($item));
    $viewData->addData('content', '');

    $viewData->addTranslation('selectvalue', $translator->translate('Select a value...'));

    return $view->render($response, 'genericForm.html.twig', (array)$viewData);
  }
}
