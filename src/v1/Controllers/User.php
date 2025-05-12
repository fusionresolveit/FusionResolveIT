<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostUser;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Certificate;
use App\Traits\Subs\Change;
use App\Traits\Subs\Document;
use App\Traits\Subs\Externallink;
use App\Traits\Subs\History;
use App\Traits\Subs\Problem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class User extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use Certificate;
  use Externallink;
  use Document;
  use History;
  use Problem;
  use Change;

  protected $model = \App\Models\User::class;
  protected $rootUrl2 = '/users/';
  protected $choose = 'users';

  protected function instanciateModel(): \App\Models\User
  {
    return new \App\Models\User();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostUser((object) $request->getParsedBody());

    $user = new \App\Models\User();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($user))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $dataToAdd = $data->exportToArray();
    if (
        (
          !empty($dataToAdd['new_password']) &&
          !empty($dataToAdd['new_password_verification'])
        ) &&
        $dataToAdd['new_password'] == $dataToAdd['new_password_verification']
    )
    {
      $dataToAdd['password'] = \App\v1\Controllers\Token::generateDBHashPassword($dataToAdd['new_password']);
    }
    unset($dataToAdd['new_password']);
    unset($dataToAdd['new_password_verification']);
    unset($dataToAdd['authsso']);

    $user = \App\Models\User::create($dataToAdd);

    \App\v1\Controllers\Toolbox::addSessionMessage('The user has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($user, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/users/' . $user->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/users')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostUser((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $user = \App\Models\User::where('id', $id)->first();
    if (is_null($user))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($user))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $dataToUpdate = $data->exportToArray();
    if (
        (
          !empty($dataToUpdate['new_password']) &&
          !empty($dataToUpdate['new_password_verification'])
        ) &&
        $dataToUpdate['new_password'] == $dataToUpdate['new_password_verification']
    )
    {
      $dataToUpdate['password'] = \App\v1\Controllers\Token::generateDBHashPassword($dataToUpdate['new_password']);
    }
    unset($dataToUpdate['new_password']);
    unset($dataToUpdate['new_password_verification']);
    unset($dataToUpdate['authsso']);

    $user->update($dataToUpdate);

    \App\v1\Controllers\Toolbox::addSessionMessage('The user has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($user, 'update');

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
    $user = \App\Models\User::withTrashed()->where('id', $id)->first();
    if (is_null($user))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($user->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $user->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The user has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/users')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $user->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The user has been soft deleted successfully');
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
    $user = \App\Models\User::withTrashed()->where('id', $id)->first();
    if (is_null($user))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($user->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $user->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The user has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubAuthorization(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\User();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('profiles')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('User not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/authorization');

    $profiles = [];
    foreach ($myItem->profiles as $profile)
    {
      $entity = \App\Models\Entity::where('id', $profile->getRelationValue('pivot')->entity_id)->first();
      if (!is_null($entity))
      {
        $profiles[] = [
          'id'      => $profile->id,
          'name'    => $profile->name,
          'entity'  => [
            'id'    => $profile->getRelationValue('pivot')->entity_id,
            'name'  => $entity->name,
          ],
          'is_recursive' => $profile->getRelationValue('pivot')->is_recursive,
        ];
      }
    }

    $form = [
      [
        'id'        => 1,
        'title'     => $translator->translatePlural('Entity', 'Entities', 1),
        'type'      => 'dropdown_remote',
        'name'      => 'entity',
        'dbname'    => 'entity_id',
        'itemtype'  => '\App\Models\Entity',
        'fillable'  => true,
        'display'   => true,
      ],
      [
        'id'        => 3,
        'title'     => $translator->translate('Child entities'),
        'type'      => 'boolean',
        'name'      => 'is_recursive',
        'fillable'  => true,
        'display'   => true,
      ],
      [
        'id'        => 2,
        'title'     => $translator->translatePlural('Profile', 'Profiles', 1),
        'type'      => 'dropdown_remote',
        'name'      => 'profile',
        'dbname'    => 'profile_id',
        'itemtype'  => '\App\Models\Profile',
        'fillable'  => true,
        'display'   => true,
      ],
    ];

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('profiles', $profiles);
    $viewData->addData('form', $form);
    $viewData->addData('csrf', \App\v1\Controllers\Toolbox::generateCSRF($request));

    return $view->render($response, 'subitem/authorization.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function itemSubAuthorization(Request $request, Response $response, array $args): Response
  {
    $data = new \App\DataInterface\PostUserItemSubAuthorization((object) $request->getParsedBody());

    if ($data->delete)
    {
      $user = \App\Models\User::where('id', $args['id'])->first();
      if (is_null($user))
      {
        throw new \Exception('User not found', 404);
      }
      $user->profiles()
        ->wherePivot('entity_id', $data->entityId)
        ->wherePivot('is_recursive', $data->recursive)
        ->detach($data->profileId);
    }
    else
    {
      $user = \App\Models\User::where('id', $args['id'])->first();
      $profile = \App\Models\Profile::where('id', $data->profileId)->first();
      $entity = \App\Models\Entity::where('id', $data->entityId)->first();
      if (!is_null($user) && !is_null($profile) && !is_null($entity))
      {
        $user->profiles()->attach(
          $profile->id,
          ['is_recursive' => $data->recursive, 'entity_id' => $entity->id]
        );
      }
    }

    $uri = $request->getUri();
    return $response
      ->withHeader('Location', (string) $uri);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubGroups(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\User();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('group')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('User not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/groups');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myGroups = [];
    foreach ($myItem->group as $group)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $group->getRelationValue('pivot')->group_id);

      if ($group->getRelationValue('pivot')->is_dynamic == 1)
      {
        $auto_val = $translator->translate('Yes');
      }
      else
      {
        $auto_val = $translator->translate('No');
      }

      if ($group->getRelationValue('pivot')->is_manager == 1)
      {
        $is_manager_val = $translator->translate('Yes');
      }
      else
      {
        $is_manager_val = $translator->translate('No');
      }

      if ($group->getRelationValue('pivot')->is_userdelegate == 1)
      {
        $is_userdelegate_val = $translator->translate('Yes');
      }
      else
      {
        $is_userdelegate_val = $translator->translate('No');
      }

      $myGroups[] = [
        'name'                    => $group->completename,
        'url'                     => $url,
        'auto'                    => $group->getRelationValue('pivot')->is_dynamic,
        'auto_val'                => $auto_val,
        'is_manager'              => $group->getRelationValue('pivot')->is_manager,
        'is_manager_val'          => $is_manager_val,
        'is_userdelegate'         => $group->getRelationValue('pivot')->is_userdelegate,
        'is_userdelegate_val'     => $is_userdelegate_val,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('groups', $myGroups);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('auto', $translator->translate('Automatic inventory'));
    $viewData->addTranslation('manager', $translator->translate('Manager'));
    $viewData->addTranslation('userdelegate', $translator->translate('Delegatee'));

    return $view->render($response, 'subitem/groups.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubReservations(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\User();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }
    $myItem2 = \App\Models\Reservation::where('user_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/reservations');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myReservations = [];
    $myReservations_old = [];
    foreach ($myItem2 as $current_reservation)
    {
      $reservationitem = \App\Models\Reservationitem::where('id', $current_reservation->reservationitem_id)->first();
      if (!is_null($reservationitem))
      {
        $item_type = $reservationitem->item_type;
        if (!class_exists($item_type))
        {
          continue;
        }

        if (
            $item_type !== \App\Models\Computer::class &&
            $item_type !== \App\Models\Networkequipment::class &&
            $item_type !== \App\Models\Passivedcequipment::class &&
            $item_type !== \App\Models\Computer::class &&
            $item_type !== \App\Models\Peripheral::class &&
            $item_type !== \App\Models\Phone::class &&
            $item_type !== \App\Models\Printer::class
        )
        {
          continue;
        }
        $item4 = new $item_type();
        $myItem4 = $item4->where('id', $reservationitem->item_id)->first();
        if ($myItem4 !== null)
        {
          $type = $item4->getTable();

          $begin = $current_reservation->begin;

          $end = $current_reservation->end;

          $user = '';
          $user_url = '';
          if ($current_reservation->user !== null)
          {
            $user = $this->genereUserName(
              $current_reservation->user->name,
              $current_reservation->user->lastname,
              $current_reservation->user->firstname
            );
            $user_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $current_reservation->user->id);
          }

          $comment = $current_reservation->comment;

          $item_name = $myItem4->getAttribute('name');
          if ($item_name == '')
          {
            $item_name = '(' . $myItem4->getAttribute('id') . ')';
          }

          $item_url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $myItem4->getAttribute('id'));

          $entity = '';
          $entity_url = '';
          if ($reservationitem->entity !== null)
          {
            $entity = $reservationitem->entity->completename;
            $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $reservationitem->entity->id);
          }

          if ($end < date('Y-m-d H:i:s'))
          {
            $myReservations_old[] = [
              'begin'         => $begin,
              'end'           => $end,
              'user'          => $user,
              'user_url'      => $user_url,
              'comment'       => $comment,
              'item_name'     => $item_name,
              'item_url'      => $item_url,
              'entity'        => $entity,
              'entity_url'    => $entity_url,
            ];
          }
          else
          {
            $myReservations[] = [
              'begin'       => $begin,
              'end'         => $end,
              'user'        => $user,
              'user_url'    => $user_url,
              'comment'     => $comment,
              'item_name'     => $item_name,
              'item_url'      => $item_url,
              'entity'        => $entity,
              'entity_url'    => $entity_url,
            ];
          }
        }
      }
    }

    // tri par ordre + ancien
    array_multisort(
      array_column($myReservations, 'begin'),
      SORT_ASC,
      SORT_NATURAL | SORT_FLAG_CASE,
      $myReservations
    );
    array_multisort(
      array_column($myReservations_old, 'begin'),
      SORT_DESC,
      SORT_NATURAL | SORT_FLAG_CASE,
      $myReservations_old
    );

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('reservations', $myReservations);
    $viewData->addData('reservations_old', $myReservations_old);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('start_date', $translator->translate('Start date'));
    $viewData->addTranslation('end_date', $translator->translate('End date'));
    $viewData->addTranslation('by', $translator->translate('By'));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));
    $viewData->addTranslation('current_reservations', $translator->translate('Current and future reservations'));
    $viewData->addTranslation('past_reservations', $translator->translate('Past reservations'));
    $viewData->addTranslation('no_reservations', $translator->translate('No reservation'));
    $viewData->addTranslation('item', $translator->translatePlural('Item', 'Items', 1));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));

    return $view->render($response, 'subitem/reservations.html.twig', (array)$viewData);
  }

  public function runRules(PostUser $data, int|null $id = null): PostUser
  {
    // Run user rules
    $rule = new \App\v1\Controllers\Rules\User();
    if (is_null($id))
    {
      $user = new \App\Models\User();
    } else {
      $user = \App\Models\User::where('id', $id)->first();
      if (is_null($user))
      {
        throw new \Exception('Id not found', 404);
      }
    }

    $data = $rule->prepareData($user, $data);
    return $rule->processAllRules($data);
  }
}
