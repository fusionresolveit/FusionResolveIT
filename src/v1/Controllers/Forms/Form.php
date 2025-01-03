<?php

declare(strict_types=1);

namespace App\v1\Controllers\Forms;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

final class Form extends \App\v1\Controllers\Common
{
  protected $model = '\App\Models\Forms\Form';
  protected $rootUrl2 = '/forms/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Forms\Form();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Forms\Form();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Forms\Form();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubSections(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('sections')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/sections');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $sections = [];
    foreach ($myItem->sections as $section)
    {
      $sections[$section->id] = [
        'id'    => $section->id,
        'name'  => $section->name,
      ];

      $item2 = new \App\Models\Forms\Section();
      $myItem2 = $item2::withCount('questions')->find($section->id);
      $sections[$section->id]['questions_count'] = $myItem2->questions_count;
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('sections', $sections);
    $viewData->addData('src', 'form');
    $viewData->addData('rootUrl2', $rootUrl2);

    $viewData->addTranslation('section', $translator->translatePlural('Section', 'Sections', 1));
    $viewData->addTranslation('nb_questions', $translator->translate('Nombre de questions'));

    return $view->render($response, 'subitem/sections.html.twig', (array)$viewData);
  }

  public function showSubQuestions(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('sections')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/questions');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $sections = [];
    foreach ($myItem->sections as $section)
    {
      $item2 = new \App\Models\Forms\Section();
      $myItem2 = $item2::with('questions')->find($section->id);

      $sections[$section->id] = [];
      $sections[$section->id]['id'] = $section->id;
      $sections[$section->id]['name'] = $section->name;

      foreach ($myItem2->questions as $question)
      {
        $sections[$section->id]['questions'][] = [
          'id'    => $question->id,
          'name'  => $question->name,
        ];
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('sections', $sections);
    $viewData->addData('src', 'form');
    $viewData->addData('rootUrl2', $rootUrl2);

    $viewData->addTranslation('section', $translator->translatePlural('Section', 'Sections', 1));
    $viewData->addTranslation('question', $translator->translatePlural('Question', 'Questions', 1));

    return $view->render($response, 'subitem/questions.html.twig', (array)$viewData);
  }

  public function showSubAnswers(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('sections')->find($args['id']);

    $item2 = new \App\Models\Forms\Answer();
    $myItem2 = $item2::with('user')->where('form_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/answers');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $answers = [];
    foreach ($myItem2 as $answer)
    {
      $user = $this->genereUserName($answer->user->name, $answer->user->lastname, $answer->user->firstname, true);

      $answers[$answer->id] = [
        'id'          => $answer->id,
        'created_at'  => $answer->created_at,
        'user'        => $user,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('answers', $answers);
    $viewData->addData('src', 'form');
    $viewData->addData('rootUrl2', $rootUrl2);

    $viewData->addTranslation('date_creation', $translator->translate('Creation date'));
    $viewData->addTranslation('create_by', $translator->translate('By user'));

    return $view->render($response, 'subitem/answers.html.twig', (array)$viewData);
  }
}
