<?php

declare(strict_types=1);

namespace App\Traits\Subs;

use App\DataInterface\Definition;
use App\DataInterface\DefinitionCollection;
use App\DataInterface\PostProfileview;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

trait Profileview
{
  /**
   * @param array<string, string> $args
   */
  public function showSubprofileview(Request $request, Response $response, array $args): Response
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
    $rootUrl = rtrim($rootUrl, '/profileview');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('profiles', $myItem->profilesview);
    $viewData->addData('show', $this->choose);

    $defColl = new DefinitionCollection();
    $defColl->add(new Definition(
      10001,
      'profile',
      'dropdown_remote',
      'profile',
      dbname: 'profile_id',
      itemtype: '\App\Models\Profile',
      fillable: true,
      // TODO manage values to prevent display in dropdown values yet in values
    ));

    $viewData->addData('form', $defColl);
    $viewData->addData('profileActions', true);
    $viewData->addData('csrf', \App\v1\Controllers\Toolbox::generateCSRF($request));

    $viewData->addTranslation('id', $translator->translate('id'));
    $viewData->addTranslation('name', $translator->translate('Name'));

    return $view->render($response, 'subitem/profileview.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function newSubProfileview(Request $request, Response $response, array $args): Response
  {
    $data = new PostProfileview((object) $request->getParsedBody());

    $item = $this->instanciateModel();
    $myItem = $item::where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $dataSync = $data->exportToArray();
    $uri = $request->getUri();

    if (!is_null($dataSync['profile']))
    {
      foreach ($myItem->profilesview as $profile)
      {
        if ($profile->id == $dataSync['profile']->id)
        {
          return $response
            ->withHeader('Location', (string) $uri)
            ->withStatus(302);
        }
      }

      $myItem->profilesview()->attach($dataSync['profile']->id);
    }

    return $response
      ->withHeader('Location', (string) $uri)
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function deleteSubProfileview(Request $request, Response $response, array $args): Response
  {
    $item = $this->instanciateModel();
    $myItem = $item::where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $myItem->profilesview()->detach($args['profileid']);

    $uri = $request->getUri();
    $url = str_replace('/delete/' . $args['profileid'], '', (string) $uri);
    return $response
      ->withHeader('Location', $url)
      ->withStatus(302);
  }
}
