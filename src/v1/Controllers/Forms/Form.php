<?php

declare(strict_types=1);

namespace App\v1\Controllers\Forms;

use App\DataInterface\PostFormForm;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Form extends \App\v1\Controllers\Common
{
  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Forms\Form::class;
  protected $rootUrl2 = '/forms/';

  protected function instanciateModel(): \App\Models\Forms\Form
  {
    return new \App\Models\Forms\Form();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostFormForm((object) $request->getParsedBody());

    $form = new \App\Models\Forms\Form();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($form))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $form = \App\Models\Forms\Form::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The form has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($form, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/forms/' . $form->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/forms')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostFormForm((object) $request->getParsedBody());
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $form = \App\Models\Forms\Form::where('id', $id)->first();
    if (is_null($form))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($form))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $form->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The form has been updated successfully');
    \App\v1\Controllers\Notification::prepareNotification($form, 'update');

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
    $form = \App\Models\Forms\Form::withTrashed()->where('id', $id)->first();
    if (is_null($form))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($form->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $form->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The form has been deleted successfully');

      return $response
        ->withHeader('Location', $basePath . '/view/forms')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $form->delete();
      \App\v1\Controllers\Toolbox::addSessionMessage('The form has been soft deleted successfully');
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
    $form = \App\Models\Forms\Form::withTrashed()->where('id', $id)->first();
    if (is_null($form))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($form->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $form->restore();
      \App\v1\Controllers\Toolbox::addSessionMessage('The form has been restored successfully');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubSections(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Forms\Form();
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

      $fSection = \App\Models\Forms\Section::withCount('questions')->where('id', $section->id)->first();
      if (!is_null($fSection))
      {
        $sections[$section->id]['questions_count'] = $fSection->questions_count;
      }
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

  /**
   * @param array<string, string> $args
   */
  public function showSubQuestions(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Forms\Form();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('sections')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->genereRootUrl($request, '/questions');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $sections = [];
    foreach ($myItem->sections as $section)
    {
      $fSection = \App\Models\Forms\Section::with('questions')->where('id', $section->id)->first();
      if (is_null($fSection))
      {
        continue;
      }

      $sections[$section->id] = [];
      $sections[$section->id]['id'] = $section->id;
      $sections[$section->id]['name'] = $section->name;

      foreach ($fSection->questions as $question)
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

  /**
   * @param array<string, string> $args
   */
  public function showSubAnswers(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $item = new \App\Models\Forms\Form();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('sections')->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $fAnswer = \App\Models\Forms\Answer::with('user')->where('form_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/answers');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $answers = [];
    foreach ($fAnswer as $answer)
    {
      $user = '';
      if (!is_null($answer->user))
      {
        $user = $this->genereUserName($answer->user->name, $answer->user->lastname, $answer->user->firstname, true);
      }

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
