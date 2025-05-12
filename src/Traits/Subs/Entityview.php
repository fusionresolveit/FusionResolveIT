<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use App\DataInterface\Definition;
use App\DataInterface\DefinitionCollection;
use App\DataInterface\PostEntityview;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Entityview
{
  /**
   * @param array<string, string> $args
   */
  public function showSubEntityview(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = $this->instanciateModel();
    $view = Twig::fromRequest($request);

    $myItem = $item::where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/entityview');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('entities', $myItem->entitiesview);
    $viewData->addData('show', $this->choose);

    $defColl = new DefinitionCollection();
    $defColl->add(new Definition(
      10001,
      'Entity',
      'dropdown_remote',
      'entity',
      dbname: 'entity_id',
      itemtype: '\App\Models\Entity',
      fillable: true,
      // TODO manage values to prevent display in dropdown values yet in values
    ));
    $defColl->add(new Definition(10002, 'recursive', 'boolean', 'is_recursive', fillable: true));

    $viewData->addData('form', $defColl);
    $viewData->addData('entityActions', true);
    $viewData->addData('csrf', \App\v1\Controllers\Toolbox::generateCSRF($request));

    $viewData->addTranslation('id', $translator->translate('id'));
    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('recursive', $translator->translate('Child entities'));

    return $view->render($response, 'subitem/entityview.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function newSubEntityview(Request $request, Response $response, array $args): Response
  {
    $data = new PostEntityview((object) $request->getParsedBody());

    $item = $this->instanciateModel();
    $myItem = $item::where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $dataSync = $data->exportToArray();
    $uri = $request->getUri();

    if (!is_null($dataSync['entity']))
    {
      foreach ($myItem->entitiesview as $entity)
      {
        if ($entity->id == $dataSync['entity']->id)
        {
          return $response
            ->withHeader('Location', (string) $uri)
            ->withStatus(302);
        }
      }

      $myItem->entitiesview()->attach($dataSync['entity']->id, ['is_recursive' => $dataSync['is_recursive']]);
    }

    return $response
      ->withHeader('Location', (string) $uri)
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function deleteSubEntityview(Request $request, Response $response, array $args): Response
  {
    $item = $this->instanciateModel();
    $myItem = $item::where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $myItem->entitiesview()->detach($args['entityid']);

    $uri = $request->getUri();
    $url = str_replace('/delete/' . $args['entityid'], '', (string) $uri);
    return $response
      ->withHeader('Location', $url)
      ->withStatus(302);
  }
}
