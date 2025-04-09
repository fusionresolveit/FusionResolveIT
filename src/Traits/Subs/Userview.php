<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use App\DataInterface\Definition;
use App\DataInterface\DefinitionCollection;
use App\DataInterface\PostUserview;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Userview
{
  /**
   * @param array<string, string> $args
   */
  public function showSubUserview(Request $request, Response $response, array $args): Response
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
    $rootUrl = rtrim($rootUrl, '/userview');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('users', $myItem->usersview);
    $viewData->addData('show', $this->choose);

    $defColl = new DefinitionCollection();
    $defColl->add(new Definition(
      10001,
      'user',
      'dropdown_remote',
      'user',
      dbname: 'user_id',
      itemtype: '\App\Models\User',
      fillable: true,
      // TODO manage values to prevent display in dropdown values yet in values
    ));

    $viewData->addData('form', $defColl);
    $viewData->addData('userActions', true);

    $viewData->addTranslation('id', $translator->translate('id'));
    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('completename', $translator->translate('Name'));

    return $view->render($response, 'subitem/userview.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function newSubUserview(Request $request, Response $response, array $args): Response
  {
    $data = new PostUserview((object) $request->getParsedBody());

    $item = $this->instanciateModel();
    $myItem = $item::where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $dataSync = $data->exportToArray();
    $uri = $request->getUri();

    if (!is_null($dataSync['user']))
    {
      foreach ($myItem->usersview as $user)
      {
        if ($user->id == $dataSync['user']->id)
        {
          return $response
            ->withHeader('Location', (string) $uri)
            ->withStatus(302);
        }
      }

      $myItem->usersview()->attach($dataSync['user']->id);
    }

    return $response
      ->withHeader('Location', (string) $uri)
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function deleteSubUserview(Request $request, Response $response, array $args): Response
  {
    $item = $this->instanciateModel();
    $myItem = $item::where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $myItem->usersview()->detach($args['userid']);

    $uri = $request->getUri();
    $url = str_replace('/delete/' . $args['userid'], '', (string) $uri);
    return $response
      ->withHeader('Location', $url)
      ->withStatus(302);
  }
}
