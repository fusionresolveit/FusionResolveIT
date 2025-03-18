<?php

declare(strict_types=1);

namespace App\v1\Controllers\Forms;

use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Section extends \App\v1\Controllers\Common
{
  // Display
  use ShowItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Forms\Section::class;
  protected $rootUrl2 = '/sections/';

  protected function instanciateModel(): \App\Models\Forms\Section
  {
    return new \App\Models\Forms\Section();
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubQuestions(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Forms\Section();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('questions')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/questions');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $questions = [];
    foreach ($myItem->questions as $question)
    {
      $questions[] = [
        'id'    => $question->id,
        'name'  => $question->name,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('questions', $questions);
    $viewData->addData('src', 'section');
    $viewData->addData('rootUrl2', $rootUrl2);

    $viewData->addTranslation('question', $translator->translatePlural('Question', 'Questions', 1));

    return $view->render($response, 'subitem/questions.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubForms(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Forms\Section();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('forms')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/forms');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $forms = [];
    foreach ($myItem->forms as $form)
    {
      $forms[] = [
        'id'    => $form->id,
        'name'  => $form->name,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('forms', $forms);
    $viewData->addData('src', 'section');
    $viewData->addData('rootUrl2', $rootUrl2);

    $viewData->addTranslation('name', $translator->translate('Name'));

    return $view->render($response, 'subitem/forms.html.twig', (array)$viewData);
  }

  /**
   * @param \App\Models\Forms\Section $item
   *
   * @return array<mixed>
   */
  protected function getInformationTop($item, Request $request): array
  {
    global $translator;

    $myItem = $item::with('forms')->where('id', $item->id)->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $tabInfos = [];
    foreach ($myItem->forms as $form)
    {
      $tabInfos[] =
        [
          'key'   => 'form_' . $form->id,
          'value' => $translator->translate('Formulaire impacté') . ' : ' . $form->name,
          'link'  => null,
        ];
    }

    return $tabInfos;
  }
}
