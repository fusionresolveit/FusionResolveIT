<?php

declare(strict_types=1);

namespace App\Traits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait ShowItem
{
  /**
   * @param array<string, string> $args
   */
  public function showItem(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item->withTrashed()->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($myItem))
    {
      throw new \Exception('Unauthorized access', 401);
    }
    $title = '';
    $fields = $item->getFormData($myItem);
    foreach ($fields as $field)
    {
      if ($field->name == 'name')
      {
        $title = $field->value;
        break;
      }
    }

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($this->getUrlWithoutQuery($request)));

    $viewData->addData('fields', $fields);
    $viewData->addData('title', $title);

    $viewData->addTranslation('selectvalue', $translator->translate('Select a value...'));

    // Information TOP
    $informations = $this->getInformationTop($myItem, $request);
    foreach ($informations as $info)
    {
      $viewData->addInformation('top', $info['key'], $info['value'], $info['link']);
    }

    // Information BOTTOM
    $informations = $this->getInformationBottom($myItem, $request);
    foreach ($informations as $info)
    {
      $viewData->addInformation('bottom', $info['key'], $info['value'], $info['link']);
    }

    return $view->render($response, 'genericForm.html.twig', (array)$viewData);
  }
}
