<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostGroup;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\Change;
use App\Traits\Subs\History;
use App\Traits\Subs\Note;
use App\Traits\Subs\Problem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Group extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use Note;
  use History;
  use Problem;
  use Change;

  protected $model = \App\Models\Group::class;
  protected $rootUrl2 = '/groups/';
  protected $choose = 'groups';

  protected function instanciateModel(): \App\Models\Group
  {
    return new \App\Models\Group();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostGroup((object) $request->getParsedBody());

    $group = new \App\Models\Group();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($group))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $group = \App\Models\Group::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The group has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($group, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/groups/' . $group->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/groups')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostGroup((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $group = \App\Models\Group::where('id', $id)->first();
    if (is_null($group))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($group))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $group->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The group has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($group, 'update');

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
    $group = \App\Models\Group::withTrashed()->where('id', $id)->first();
    if (is_null($group))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($group->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $group->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The group has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/groups')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $group->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The group has been soft deleted successfully');
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
    $group = \App\Models\Group::withTrashed()->where('id', $id)->first();
    if (is_null($group))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($group->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $group->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The group has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubUsers(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Group();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $item2 = new \App\Models\User();
    $myItem2 = $item2::with('group')->get();

    $rootUrl = $this->genereRootUrl($request, '/users');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myUsers = [];
    foreach ($myItem2 as $user)
    {
      if ($user->group !== null)
      {
        foreach ($user->group as $group)
        {
          if ($group->getRelationValue('pivot')->group_id == $args['id'])
          {
            $user_name = $this->genereUserName($user->name, $user->lastname, $user->firstname);

            $url = $this->genereRootUrl2Link($rootUrl2, '/users/', $group->getRelationValue('pivot')->user_id);

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

            if ($user->is_active == 1)
            {
              $is_active_val = $translator->translate('Yes');
            }
            else
            {
              $is_active_val = $translator->translate('No');
            }

            $myUsers[$group->getRelationValue('pivot')->user_id] = [
              'name'                  => $user_name,
              'url'                   => $url,
              'auto'                  => $group->getRelationValue('pivot')->is_dynamic,
              'auto_val'              => $auto_val,
              'is_manager'            => $group->getRelationValue('pivot')->is_manager,
              'is_manager_val'        => $is_manager_val,
              'is_userdelegate'       => $group->getRelationValue('pivot')->is_userdelegate,
              'is_userdelegate_val'   => $is_userdelegate_val,
              'is_active'             => $user->is_active,
              'is_active_val'         => $is_active_val,
            ];
          }
        }
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('users', $myUsers);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('auto', $translator->translate('Automatic inventory'));
    $viewData->addTranslation('manager', $translator->translate('Manager'));
    $viewData->addTranslation('userdelegate', $translator->translate('Delegatee'));
    $viewData->addTranslation('active', $translator->translate('Active'));

    return $view->render($response, 'subitem/users.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubGroups(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Group();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('parents')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/groups');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myGroups = [];
    foreach ($myItem->parents as $parent)
    {
      $name = $parent->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $parent->id);

      $entity = '';
      $entity_url = '';
      if ($parent->entity !== null)
      {
        $entity = $parent->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $parent->entity->id);
      }

      $comment = $parent->comment;

      $myGroups[$parent->id] = [
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
    $viewData->addData('groups', $myGroups);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));

    return $view->render($response, 'subitem/groups.html.twig', (array)$viewData);
  }
}
