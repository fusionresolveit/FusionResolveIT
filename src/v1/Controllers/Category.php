<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Category extends Common
{
  protected $model = '\App\Models\Category';
  protected $rootUrl2 = '/dropdown/categories/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Category();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Category();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Category();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubCategories(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new $this->model();
    $myItem2 = $item2::with('template_request', 'template_incident', 'template_change', 'template_problem')->where('category_id', $args['id'])->get();

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/categories');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myCategories = [];
    foreach ($myItem2 as $current_category)
    {
      $name = $current_category->name;

      $url = '';
      if ($rootUrl2 != '') {
        $url = $rootUrl2 . "/dropdown/categories/" . $current_category->id;
      }

      $entity = '';
      if ($current_category->entity != null) {
        $entity = $current_category->entity->name;
      }

      $user = '';
      if ($current_category->users != null) {
        $user = $current_category->users->name;
      }

      $group = '';;
      if ($current_category->groups != null) {
        $group = $current_category->groups->name;
      }

      $knowbaseitemcategory = '';
      if ($current_category->knowbaseitemcategories != null) {
        $knowbaseitemcategory = $current_category->knowbaseitemcategories->name;
      }

      $visible_simplified_interface = $current_category->is_helpdeskvisible;
      if ($current_category->is_helpdeskvisible == 1)
      {
        $visible_simplified_interface_val = $translator->translate('Yes');
      }
      else
      {
        $visible_simplified_interface_val = $translator->translate('No');
      }

      $visible_incident = $current_category->is_incident;
      if ($current_category->is_incident == 1)
      {
        $visible_incident_val = $translator->translate('Yes');
      }
      else
      {
        $visible_incident_val = $translator->translate('No');
      }

      $visible_request = $current_category->is_request;
      if ($current_category->is_request == 1)
      {
        $visible_request_val = $translator->translate('Yes');
      }
      else
      {
        $visible_request_val = $translator->translate('No');
      }

      $visible_problem = $current_category->is_problem;
      if ($current_category->is_request == 1)
      {
        $visible_problem_val = $translator->translate('Yes');
      }
      else
      {
        $visible_problem_val = $translator->translate('No');
      }

      $visible_change = $current_category->is_change;
      if ($current_category->is_request == 1)
      {
        $visible_change_val = $translator->translate('Yes');
      }
      else
      {
        $visible_change_val = $translator->translate('No');
      }

      $template_request = '';
      if ($current_category->tickettemplatesDemand != null) {
        $template_request = $current_category->tickettemplatesDemand->name;
      }
      $template_incident = '';
      if ($current_category->tickettemplatesIncident != null) {
        $template_incident = $current_category->tickettemplatesIncident->name;
      }
      $template_change = '';
      if ($current_category->changetemplates != null) {
        $template_change = $current_category->changetemplates->name;
      }
      $template_problem = '';
      if ($current_category->problemtemplates != null) {
        $template_problem = $current_category->problemtemplates->name;
      }
      $comment = $current_category->comment;

      $myCategories[$current_category->id] = [
        'name'                                => $name,
        'url'                                 => $url,
        'entity'                              => $entity,
        'user'                                => $user,
        'group'                               => $group,
        'knowbaseitemcategory'                => $knowbaseitemcategory,
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
        'template_incident'                   => $template_incident,
        'template_change'                     => $template_change,
        'template_problem'                    => $template_problem,
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
    $viewData->addTranslation('visible_simplified_interface', $translator->translate('Visible in the simplified interface'));
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
