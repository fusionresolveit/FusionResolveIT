<?php

declare(strict_types=1);

namespace App\v1\Controllers\Forms;

use App\Traits\ShowItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Question extends \App\v1\Controllers\Common
{
  // Display
  use ShowItem;

  // Sub
  use History;

  protected $model = \App\Models\Forms\Question::class;
  protected $rootUrl2 = '/questions/';

  protected function instanciateModel(): \App\Models\Forms\Question
  {
    return new \App\Models\Forms\Question();
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubSections(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Forms\Question();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('sections')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/sections');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $sections = [];
    foreach ($myItem->sections as $section)
    {
      $sections[$section->id] = [
        'id'    => $section->id,
        'name'  => $section->name,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('sections', $sections);
    $viewData->addData('src', 'question');
    $viewData->addData('rootUrl2', $rootUrl2);

    $viewData->addTranslation('name', $translator->translate('Name'));

    return $view->render($response, 'subitem/sections.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubForms(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Forms\Question();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('sections')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/forms');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $forms = [];
    foreach ($myItem->sections as $section)
    {
      $myItem2 = \App\Models\Forms\Section::with('forms')->where('id', $section->id)->first();
      if (is_null($myItem2))
      {
        continue;
      }

      foreach ($myItem2->forms as $form)
      {
        $forms[$form->id] = [
          'id'    => $form->id,
          'name'  => $form->name,
        ];
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('forms', $forms);
    $viewData->addData('src', 'question');
    $viewData->addData('rootUrl2', $rootUrl2);

    $viewData->addTranslation('name', $translator->translate('Name'));

    return $view->render($response, 'subitem/forms.html.twig', (array)$viewData);
  }

  /**
   * @param \App\Models\Forms\Question $item
   *
   * @return array<mixed>
   */
  protected function getInformationTop($item, Request $request): array
  {
    global $translator;

    $myItem = $item::with('sections')->where('id', $item->id)->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $tabInfos = [];

    foreach ($myItem->sections as $section)
    {
      $tabInfos[] =
        [
          'key'   => 'section_' . $section->id,
          'value' => $translator->translate('Section impactée') . ' : ' . $section->name,
          'link'  => null,
        ];
    }

    $forms = [];
    foreach ($myItem->sections as $section)
    {
      $myItem2 = \App\Models\Forms\Section::with('forms')->where('id', $section->id)->first();
      if (is_null($myItem2))
      {
        continue;
      }
      foreach ($myItem2->forms as $form)
      {
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
