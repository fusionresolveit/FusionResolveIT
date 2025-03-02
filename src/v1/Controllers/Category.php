<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\DataInterface\PostCategory;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Category extends Common implements \App\Interfaces\Crud
{
  // Display
  use ShowItem;
  use ShowNewItem;

  // Sub
  use History;

  protected $model = \App\Models\Category::class;
  protected $rootUrl2 = '/dropdown/categories/';

  protected function instanciateModel(): \App\Models\Category
  {
    return new \App\Models\Category();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostCategory((object) $request->getParsedBody());

    $category = new \App\Models\Category();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($category))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $category = \App\Models\Category::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The category has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($category, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/categories/' . $category->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/categories')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostCategory((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $category = \App\Models\Category::where('id', $id)->first();
    if (is_null($category))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($category))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $category->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The category has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($category, 'update');

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
    $category = \App\Models\Category::withTrashed()->where('id', $id)->first();
    if (is_null($category))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($category->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $category->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The category has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/categories')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $category->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The category has been soft deleted successfully');
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
    $category = \App\Models\Category::withTrashed()->where('id', $id)->first();
    if (is_null($category))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($category->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $category->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The category has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubCategories(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Category();
    $view = Twig::fromRequest($request);

    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $categories = \App\Models\Category::
        with('templaterequest', 'templateincident', 'templatechange', 'templateproblem')
      ->where('category_id', $args['id'])
      ->get();

    $rootUrl = $this->genereRootUrl($request, '/categories');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myCategories = [];
    foreach ($categories as $category)
    {
      $name = $category->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/categories/', $category->id);

      $entity = '';
      $entity_url = '';
      if ($category->entity !== null)
      {
        $entity = $category->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $category->entity->id);
      }

      $user = '';
      $user_url = '';
      if ($category->user !== null)
      {
        $user = $this->genereUserName(
          $category->user->name,
          $category->user->lastname,
          $category->user->firstname,
          false
        );
        $user_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $category->user->id);
      }

      $group = '';
      $group_url = '';
      if ($category->group !== null)
      {
        $group = $category->group->completename;
        $group_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $category->group->id);
      }

      $knowbaseitemcategory = '';
      $knowbaseitemcategory_url = '';
      if ($category->knowbaseitemcategory !== null)
      {
        $knowbaseitemcategory = $category->knowbaseitemcategory->name;
        $knowbaseitemcategory_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/knowbaseitemcategories/',
          $category->knowbaseitemcategory->id
        );
      }

      $visible_simplified_interface = $category->is_helpdeskvisible;
      if ($category->is_helpdeskvisible == 1)
      {
        $visible_simplified_interface_val = $translator->translate('Yes');
      }
      else
      {
        $visible_simplified_interface_val = $translator->translate('No');
      }

      $visible_incident = $category->is_incident;
      if ($category->is_incident == 1)
      {
        $visible_incident_val = $translator->translate('Yes');
      }
      else
      {
        $visible_incident_val = $translator->translate('No');
      }

      $visible_request = $category->is_request;
      if ($category->is_request == 1)
      {
        $visible_request_val = $translator->translate('Yes');
      }
      else
      {
        $visible_request_val = $translator->translate('No');
      }

      $visible_problem = $category->is_problem;
      if ($category->is_request == 1)
      {
        $visible_problem_val = $translator->translate('Yes');
      }
      else
      {
        $visible_problem_val = $translator->translate('No');
      }

      $visible_change = $category->is_change;
      if ($category->is_request == 1)
      {
        $visible_change_val = $translator->translate('Yes');
      }
      else
      {
        $visible_change_val = $translator->translate('No');
      }

      $template_request = '';
      $template_request_url = '';
      if ($category->tickettemplateDemand !== null)
      {
        $template_request = $category->tickettemplateDemand->name;
        $template_request_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/ticketemplates/',
          $category->tickettemplateDemand->id
        );
      }

      $template_incident = '';
      $template_incident_url = '';
      if ($category->tickettemplateIncident !== null)
      {
        $template_incident = $category->tickettemplateIncident->name;
        $template_incident_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/ticketemplates/',
          $category->tickettemplateIncident->id
        );
      }

      $template_change = '';
      $template_change_url = '';
      if ($category->changetemplate !== null)
      {
        $template_change = $category->changetemplate->name;
        $template_change_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/changetemplates/',
          $category->changetemplate->id
        );
      }

      $template_problem = '';
      $template_problem_url = '';
      if ($category->problemtemplate !== null)
      {
        $template_problem = $category->problemtemplate->name;
        $template_problem_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/problemtemplates/',
          $category->problemtemplate->id
        );
      }

      $comment = $category->comment;

      $myCategories[$category->id] = [
        'name'                                => $name,
        'url'                                 => $url,
        'entity'                              => $entity,
        'entity_url'                          => $entity_url,
        'user'                                => $user,
        'user_url'                            => $user_url,
        'group'                               => $group,
        'group_url'                           => $group_url,
        'knowbaseitemcategory'                => $knowbaseitemcategory,
        'knowbaseitemcategory_url'            => $knowbaseitemcategory_url,
        'visible_simplified_interface'        => $visible_simplified_interface,
        'visible_simplified_interface_val'    => $visible_simplified_interface_val,
        'visible_incident'                    => $visible_incident,
        'visible_incident_val'                => $visible_incident_val,
        'visible_request'                     => $visible_request,
        'visible_request_val'                 => $visible_request_val,
        'visible_problem'                     => $visible_problem,
        'visible_problem_val'                 => $visible_problem_val,
        'visible_change'                      => $visible_change,
        'visible_change_val'                  => $visible_change_val,
        'template_request'                    => $template_request,
        'template_request_url'                => $template_request_url,
        'template_incident'                   => $template_incident,
        'template_incident_url'               => $template_incident_url,
        'template_change'                     => $template_change,
        'template_change_url'                 => $template_change_url,
        'template_problem'                    => $template_problem,
        'template_problem_url'                => $template_problem_url,
        'comment'                             => $comment,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('categories', $myCategories);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('user', $translator->translate('Technician in charge of the hardware'));
    $viewData->addTranslation('group', $translator->translate('Group in charge of the hardware'));
    $viewData->addTranslation('knowbaseitemcategory', $translator->translate('Knowledge base'));
    $viewData->addTranslation(
      'visible_simplified_interface',
      $translator->translate('Visible in the simplified interface')
    );
    $viewData->addTranslation('visible_incident', $translator->translate('Visible for an incident'));
    $viewData->addTranslation('visible_request', $translator->translate('Visible for a request'));
    $viewData->addTranslation('visible_problem', $translator->translate('Visible for a problem'));
    $viewData->addTranslation('visible_change', $translator->translate('Visible for a change'));
    $viewData->addTranslation('template_request', $translator->translate('Template for a request'));
    $viewData->addTranslation('template_incident', $translator->translate('Template for an incident'));
    $viewData->addTranslation('template_change', $translator->translate('Template for a change'));
    $viewData->addTranslation('template_problem', $translator->translate('Template for a problem'));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));

    return $view->render($response, 'subitem/categories.html.twig', (array)$viewData);
  }
}
