<?php

declare(strict_types=1);

namespace App\v1\Controllers\Forms;

use App\DataInterface\PostStandard;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Section extends \App\v1\Controllers\Common
{
  // Display
  use ShowItem;
  use ShowNewItem;
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
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Forms\Section::class);

    $section = new \App\Models\Forms\Section();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($section))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $section = \App\Models\Forms\Section::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('created');
    \App\v1\Controllers\Notification::prepareNotification($section, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/sections/' . $section->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/sections')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function updateItem(Request $request, Response $response, array $args): Response
  {
    $data = new PostStandard((object) $request->getParsedBody(), \App\Models\Forms\Section::class);
    $id = intval($args['id']);

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $section = \App\Models\Forms\Section::where('id', $id)->first();
    if (is_null($section))
    {
      throw new \Exception('Id not found', 404);
    }
    if (!\App\v1\Controllers\Profile::canRightReadItem($section))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $section->update($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessageItemAction('updated');
    \App\v1\Controllers\Notification::prepareNotification($section, 'update');

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
    $section = \App\Models\Forms\Section::withTrashed()->where('id', $id)->first();
    if (is_null($section))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($section->trashed())
    {
      if (!$this->canRightDelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $section->forceDelete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('deleted');

      return $response
        ->withHeader('Location', $basePath . '/view/sections')
        ->withStatus(302);
    } else {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $section->delete();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('softdeleted');
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
    $section = \App\Models\Forms\Section::withTrashed()->where('id', $id)->first();
    if (is_null($section))
    {
      throw new \Exception('Id not found', 404);
    }

    if ($section->trashed())
    {
      if (!$this->canRightSoftdelete())
      {
        throw new \Exception('Unauthorized access', 401);
      }
      $section->restore();
      \App\v1\Controllers\Toolbox::addSessionMessageItemAction('restored');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubQuestions(Request $request, Response $response, array $args): Response
  {
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

    $viewData->addTranslation('question', npgettext('global', 'Question', 'Questions', 1));

    return $view->render($response, 'subitem/questions.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showSubForms(Request $request, Response $response, array $args): Response
  {
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

    $viewData->addTranslation('name', pgettext('global', 'Name'));

    return $view->render($response, 'subitem/forms.html.twig', (array)$viewData);
  }

  /**
   * @param \App\Models\Forms\Section $item
   *
   * @return array<mixed>
   */
  protected function getInformationTop($item, Request $request): array
  {
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
          'value' => pgettext('form', 'Form impacted') . ' : ' . $form->name,
          'link'  => null,
        ];
    }

    return $tabInfos;
  }
}
