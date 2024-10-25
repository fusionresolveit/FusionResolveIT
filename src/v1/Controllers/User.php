<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class User extends Common
{
  protected $model = '\App\Models\User';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\User();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\User();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\User();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubAuthorization(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new \App\Models\User();
    $view = Twig::fromRequest($request);

    $profiles = [];
    $myItem = $item::with('profiles')->find($args['id']);
    foreach ($myItem->profiles as $profile)
    {
      $entity = \App\Models\Entity::find($profile->pivot->entity_id);
      $profiles[] = [
        'id'      => $profile->id,
        'name'    => $profile->name,
        'entity'  => [
          'id'    => $profile->pivot->entity_id,
          'name'  => $entity->name,
        ],
        'is_recursive' => $profile->pivot->is_recursive,
      ];
    }

    $form = [
      [
        'id'    => 1,
        'title' => $translator->translatePlural('Entity', 'Entities', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'entity',
        'dbname'  => 'entity_id',
        'itemtype' => '\App\Models\Entity',
        'fillable' => true,
        'display' => true,
      ],
      [
        'id'    => 3,
        'title' => $translator->translate('Child entities'),
        'type'  => 'boolean',
        'name'  => 'is_recursive',
        'fillable' => true,
        'display' => true,
      ],
      [
        'id'    => 2,
        'title' => $translator->translatePlural('Profile', 'Profiles', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'profile',
        'dbname'  => 'profile_id',
        'itemtype' => '\App\Models\Profile',
        'fillable' => true,
        'display' => true,
      ],
    ];

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/users');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('profiles', $profiles);
    $viewData->addData('form', $form);

    return $view->render($response, 'subitem/authorization.html.twig', (array)$viewData);
  }

  public function itemSubAuthorization(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'delete'))
    {
      if (
          property_exists($data, 'entity_id') &&
          property_exists($data, 'is_recursive') &&
          property_exists($data, 'profile_id')
      )
      {
        $user = \App\Models\User::find($args['id']);
        $user->profiles()
          ->wherePivot('entity_id', $data->entity_id)
          ->wherePivot('is_recursive', $data->is_recursive)
          ->detach($data->profile_id);
      }
    } else {
      $user = \App\Models\User::find($args['id']);
      $profile = \App\Models\Profile::find($data->profile);
      $entity = \App\Models\Entity::find($data->entity);
      if (!is_null($user) && !is_null($profile) && !is_null($entity))
      {
        $recursive = false;
        if (property_exists($data, 'is_recursive') && $data->is_recursive == 'on')
        {
          $recursive = true;
        }
        $user->profiles()->attach(
          $profile->id,
          ['is_recursive' => $recursive, 'entity_id' => $entity->id]
        );
      }
    }

    $uri = $request->getUri();
    header('Location: ' . (string) $uri);
    exit();
  }
}
