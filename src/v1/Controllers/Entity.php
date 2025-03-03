<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostEntity;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Document;
use App\Traits\Subs\History;
use App\Traits\Subs\Knowbaseitem;
use App\Traits\Subs\Note;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Entity extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use Note;
  use Knowbaseitem;
  use Document;
  use History;

  protected $model = \App\Models\Entity::class;
  protected $rootUrl2 = '/entities/';

  protected function instanciateModel(): \App\Models\Entity
  {
    return new \App\Models\Entity();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostEntity((object) $request->getParsedBody());

    $entity = new \App\Models\Entity();

    if (!$this->canRightCreate()) {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($entity)) {
      throw new \Exception('Unauthorized access', 401);
    }

    $entity = \App\Models\Entity::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The entity has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($entity, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view') {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/entities/' . $entity->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/entities')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostEntity((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate()) {
      throw new \Exception('Unauthorized access', 401);
    }

    $entity = \App\Models\Entity::where('id', $id)->first();
    if (is_null($entity)) {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($entity)) {
      throw new \Exception('Unauthorized access', 401);
    }

    $entity->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The entity has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($entity, 'update');

    $uri = $request->getUri();
    return $response
      ->withHeader('Location', (string) $uri)
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function deleteItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $id = intval($args['id']);
    $entity = \App\Models\Entity::withTrashed()->where('id', $id)->first();
    if (is_null($entity)) {
      throw new \Exception('Id not found', 404);
    }

    if ($entity->trashed()) {
      if (!$this->canRightDelete()) {
        throw new \Exception('Unauthorized access', 401);
      }
      $entity->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The entity has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/entities')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete()) {
        throw new \Exception('Unauthorized access', 401);
      }
      $entity->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The entity has been soft deleted successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function restoreItem(Request $request, Response $response, array $args): Response
  {
    $id = intval($args['id']);
    $entity = \App\Models\Entity::withTrashed()->where('id', $id)->first();
    if (is_null($entity)) {
      throw new \Exception('Id not found', 404);
    }

    if ($entity->trashed()) {
      if (!$this->canRightSoftdelete()) {
        throw new \Exception('Unauthorized access', 401);
      }
      $entity->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The entity has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubAddress(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Entity();
    $view = Twig::fromRequest($request);

    $myItem = \App\Models\Entity::where('id', $args['id'])->first();
    if (is_null($myItem)) {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/address');

    $address = [
      'phonenumber'   => $myItem->phonenumber,
      'fax'           => $myItem->fax,
      'website'       => $myItem->website,
      'email'         => $myItem->email,
      'address'       => $myItem->address,
      'postcode'      => $myItem->postcode,
      'town'          => $myItem->town,
      'state'         => $myItem->state,
      'country'       => $myItem->country,
      'longitude'     => $myItem->longitude,
      'latitude'      => $myItem->latitude,
      'altitude'      => $myItem->altitude,
    ];

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $getDefs = $item->getSpecificFunction('getDefinitionAddress');
    $myItemData = [
      'phonenumber'   => $address['phonenumber'],
      'fax'           => $address['fax'],
      'website'       => $address['website'],
      'email'         => $address['email'],
      'address'       => $address['address'],
      'postcode'      => $address['postcode'],
      'town'          => $address['town'],
      'state'         => $address['state'],
      'country'       => $address['country'],
      'longitude'     => $address['longitude'],
      'latitude'      => $address['latitude'],
      'altitude'      => $address['altitude'],
    ];
    $jsonStr = json_encode($myItemData);
    if ($jsonStr === false) {
      $jsonStr = '{}';
    }
    $myItemDataObject = json_decode($jsonStr);

    $viewData->addData('fields', $item->getFormData($myItemDataObject, $getDefs));

    return $view->render($response, 'subitem/adress.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubEntities(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Entity();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem)) {
      throw new \Exception('Id not found', 404);
    }

    $item2 = new \App\Models\Entity();
    $myItem2 = $item2::where('entity_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/entities');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myEntities = [];
    foreach ($myItem2 as $child) {
      $name = $child->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $child->id);

      $entity = '';
      $entity_url = '';
      if ($child->entity !== null) {
        $entity = $child->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $child->entity->id);
      }

      $comment = $child->comment;

      $myEntities[$child->id] = [
        'name'          => $name,
        'url'           => $url,
        'entity'        => $entity,
        'entity_url'    => $entity_url,
        'comment'       => $comment,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('entities', $myEntities);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));

    return $view->render($response, 'subitem/entities.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubUsers(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Entity();
    $view = Twig::fromRequest($request);

    $rootUrl = $this->genereRootUrl($request, '/users');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myItem = $item->where('id', $args['id'])->first();

    $profiles = \App\Models\Profile::
        with('users')
      ->whereRelation('users', 'profile_user.entity_id', $args['id'])
      ->get();
    $myProfilesUsers = [];

    foreach ($profiles as $profile) {
      $myProfilesUsers[$profile->id] = [
        'name'    => $profile->name,
        'users'   => [],
      ];

      foreach ($profile->users as $user) {
        $auto = false;
        $recursive = false;
        $user_name = $this->genereUserName($user->name, $user->lastname, $user->firstname);
        $auto_val = $translator->translate('No');
        $recursive_val = $translator->translate('No');



        $myProfilesUsers[$profile->id]['users'][$user->id] = [
          'name'             => $user_name,
          'auto'             => $auto,
          'auto_val'         => $auto_val,
          'recursive'        => $recursive,
          'recursive_val'    => $recursive_val,
        ];
      }
    }

    // tri ordre alpha
    array_multisort(array_column($myProfilesUsers, 'name'), SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $myProfilesUsers);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($item, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('profilesusers', $myProfilesUsers);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('is_dynamic', $translator->translate('Dynamic'));
    $viewData->addTranslation('is_recursive', $translator->translate('Recursive'));
    $viewData->addTranslation('profil', $translator->translatePlural('Profile', 'Profiles', 1));


    return $view->render($response, 'subitem/profilesusers.html.twig', (array)$viewData);
  }
}
