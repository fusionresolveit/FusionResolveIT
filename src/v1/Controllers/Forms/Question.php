<?php

declare(strict_types=1);

namespace App\v1\Controllers\Forms;

use App\DataInterface\PostFormQuestion;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Question extends \App\v1\Controllers\Common
{
  // Display
  use ShowItem;
  use ShowAll;

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
  public function showNewItem(Request $request, Response $response, array $args): Response
  {
    global $translator;
    $view = Twig::fromRequest($request);

    $item = $this->instanciateModel();

    $data = (object) $request->getQueryParams();
    $fieldTypes = \App\Models\Definitions\Forms\Question::getFieldtype();

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($item, $request);

    // check if field 'type' exists and have value in definition->getFieldtype()
    if (isset($data->type) && isset($fieldTypes[$data->type]))
    {
      // display fields to fill

      $hideFields = $this->hideFieldsForType($data->type);
      $fields = $item->getFormData($item);
      // update field type
      foreach ($fields as &$field)
      {
        if ($field->name == 'fieldtype')
        {
          $field->value = $data->type;
          $field->readonly = true;
        }
        if (in_array($field->name, $hideFields))
        {
          $field->display = false;
        }
      }

      $viewData->addData('fields', $fields);
      $viewData->addData('content', '');

      $viewData->addTranslation('selectvalue', $translator->translate('Select a value...'));

      return $view->render($response, 'genericForm.html.twig', (array)$viewData);
    }
    // else, display choose the type, make card for each type

    $viewData->addData('types', $fieldTypes);

    return $view->render($response, 'questionTypeSelection.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostFormQuestion((object) $request->getParsedBody());

    $question = new \App\Models\Forms\Question();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($question))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $question = \App\Models\Forms\Question::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The computer model has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($question, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/questions/' . $question->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/questions')
      ->withStatus(302);
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

  /**
   * return list of fields name to hide in the form (new / update)
   * @return array<string>
   */
  private function hideFieldsForType($type): array
  {
    switch ($type) {
      case 'checkbox':
        return [
          'range_min',
          'range_max',
          'regex',
        ];

      case 'radio':
        return [
          'range_min',
          'range_max',
          'regex',
        ];

      case 'hidden':
        return [
          'range_min',
          'range_max',
          'regex',
          'default_values',
          'values',
        ];

      case 'email':
        return [
          'range_min',
          'range_max',
          'default_values',
          'values',
        ];
    
      case 'date':
        return [
          'default_values',
          'values',
        ];

      case 'description':
        return [
          'range_min',
          'range_max',
          'default_values',
          'values',
        ];

      case 'integer':
        return [
          'regex',
          'default_values',
          'values',
        ];

      case 'file':
        return [
          'regex',
          'default_values',
          'values',
        ];

      case 'float':
        return [
          'regex',
          'default_values',
          'values',
        ];

      case 'time':
        return [
          'default_values',
          'values',
        ];

      case 'dropdown':
        return [
          'range_min',
          'range_max',
          'regex',
        ];     

      case 'glpiselect':
        return [
          'regex',
        ];     

      case 'select':
        return [
          'regex',
        ];     

      case 'multiselect':
        return [
          'regex',
        ];     
           
      case 'text':
        return [
          'range_min',
          'range_max',
          'default_values',
          'values',
        ];       

      case 'urgency':
        return [
          'regex',
          'default_values',
          'values',
        ];     
          
      case 'textarea':
        return [
          'default_values',
          'values',
        ];  
    }
    return [];
  }
}
