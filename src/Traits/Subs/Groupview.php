<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use App\DataInterface\Definition;
use App\DataInterface\DefinitionCollection;
use App\DataInterface\PostGroupview;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Groupview
{
  /**
   * @param array<string, string> $args
   */
  public function showSubgroupview(Request $request, Response $response, array $args): Response
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
    $rootUrl = rtrim($rootUrl, '/groupview');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('groups', $myItem->groupsview);
    $viewData->addData('show', $this->choose);

    $defColl = new DefinitionCollection();
    $defColl->add(new Definition(
      10001,
      'group',
      'dropdown_remote',
      'group',
      dbname: 'group_id',
      itemtype: '\App\Models\Group',
      fillable: true,
      // TODO manage values to prevent display in dropdown values yet in values
    ));
    $defColl->add(new Definition(10002, 'recursive', 'boolean', 'is_recursive', fillable: true));

    $viewData->addData('form', $defColl);
    $viewData->addData('groupActions', true);

    $viewData->addTranslation('id', $translator->translate('id'));
    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('recursive', $translator->translate('Child groups'));

    return $view->render($response, 'subitem/groupview.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function newSubGroupview(Request $request, Response $response, array $args): Response
  {
    $data = new PostGroupview((object) $request->getParsedBody());

    $item = $this->instanciateModel();
    $myItem = $item::where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $dataSync = $data->exportToArray();
    $uri = $request->getUri();

    if (!is_null($dataSync['group']))
    {
      foreach ($myItem->groupsview as $group)
      {
        if ($group->id == $dataSync['group']->id)
        {
          return $response
            ->withHeader('Location', (string) $uri)
            ->withStatus(302);
        }
      }

      $myItem->groupsview()->attach($dataSync['group']->id, ['is_recursive' => $dataSync['is_recursive']]);
    }

    return $response
      ->withHeader('Location', (string) $uri)
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function deleteSubGroupview(Request $request, Response $response, array $args): Response
  {
    $item = $this->instanciateModel();
    $myItem = $item::where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $myItem->groupsview()->detach($args['groupid']);

    $uri = $request->getUri();
    $url = str_replace('/delete/' . $args['groupid'], '', (string) $uri);
    return $response
      ->withHeader('Location', $url)
      ->withStatus(302);
  }
}
