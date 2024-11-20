<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Group extends Common
{
  protected $model = '\App\Models\Group';
  protected $rootUrl2 = '/groups/';
  protected $itilchoose = 'groups';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Group();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Group();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Group();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubUsers(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\User();
    $myItem2 = $item2::with('group')->get();

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/users');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myUsers = [];
    foreach ($myItem2 as $user)
    {
      if ($user->group != null) {
        foreach ($user->group as $group) {
          if ($group->pivot->group_id == $args['id']) {
            $url = '';
            if ($rootUrl2 != '') {
              $url = $rootUrl2 . "/users/" . $group->pivot->user_id;
            }

            $myUsers[$group->pivot->user_id] = [
              'name'                 => $user->name,
              'url'                  => $url,
              'auto'                 => $group->pivot->is_dynamic,
              'is_manager'           => $group->pivot->is_manager,
              'is_userdelegate'      => $group->pivot->is_userdelegate,
              'is_active'            => $user->is_active,
            ];
          }
        }
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('users', $myUsers);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('auto', $translator->translate('Automatic inventory'));
    $viewData->addTranslation('manager', $translator->translate('Manager'));
    $viewData->addTranslation('userdelegate', $translator->translate('Delegatee'));
    $viewData->addTranslation('active', $translator->translate('Active'));

    return $view->render($response, 'subitem/users.html.twig', (array)$viewData);
  }
}
