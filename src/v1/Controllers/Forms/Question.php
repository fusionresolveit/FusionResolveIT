<?php

namespace App\v1\Controllers\Forms;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

final class Question extends \App\v1\Controllers\Common
{
  protected $model = '\App\Models\Forms\Question';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Forms\Question();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = \App\Models\Forms\Question::find($args['id']);
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Forms\Question();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubSections(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new \App\Models\Forms\Question();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('sections')->find($args['id']);

    $sections = [];
    foreach ($myItem->sections as $section) {
      $sections[$section->id] = [
        'id' => $section->id,
        'name' => $section->name,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/sections');
    $rootUrl2 = rtrim($rootUrl, '/questions/' . $args['id']);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);

    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('sections', $sections);
    $viewData->addData('src', 'question');
    $viewData->addData('rootUrl2', $rootUrl2);

    $viewData->addTranslation('name', $translator->translate('Name'));

    return $view->render($response, 'subitem/sections.html.twig', (array)$viewData);
  }

  public function showSubForms(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new \App\Models\Forms\Question();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('sections')->find($args['id']);

    $forms = [];
    foreach ($myItem->sections as $section) {
      $item2 = new \App\Models\Forms\Section;
      $myItem2 = $item2::with('forms')->find($section->id);

      foreach ($myItem2->forms as $form) {
        $forms[$form->id] = [
          'id' => $form->id,
          'name' => $form->name,
        ];
      }
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/forms');
    $rootUrl2 = rtrim($rootUrl, '/questions/' . $args['id']);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);

    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('forms', $forms);
    $viewData->addData('src', 'question');
    $viewData->addData('rootUrl2', $rootUrl2);

    $viewData->addTranslation('name', $translator->translate('Name'));

    return $view->render($response, 'subitem/forms.html.twig', (array)$viewData);
  }

  protected function getInformationTop($item, $request)
  {
    global $translator;

    $myItem = $item::with('sections')->find($item->id);

    $tabInfos = [];

    foreach ($myItem->sections as $section) {
      $tabInfos[] =
        [
          'key'   => 'section_' . $section->id,
          'value' => $translator->translate('Section impactée') . ' : ' . $section->name,
          'link'  => null,
        ];
    }

    $forms = [];
    foreach ($myItem->sections as $section) {
      $item2 = new \App\Models\Forms\Section;
      $myItem2 = $item2::with('forms')->find($section->id);

      foreach ($myItem2->forms as $form) {
        $tabInfos[] =
          [
            'key'   => 'form_' . $form->id,
            'value' => $translator->translate('Formulaire impacté') . ' : ' . $form->name,
            'link'  => null,
          ];
      }
    }

    return $tabInfos;
  }

}
