<?php

declare(strict_types=1);

namespace App\v1\Controllers\Forms;

use App\Traits\Subs\History;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Answer extends \App\v1\Controllers\Common
{
  use History;

  protected $model = \App\Models\Forms\Answer::class;

  protected function instanciateModel(): \App\Models\Forms\Answer
  {
    return new \App\Models\Forms\Answer();
  }

  /**
   * @param array<string, string> $args
   */
  public function showAnswer(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Forms\Answer();
    $view = Twig::fromRequest($request);

    $answer = \App\Models\Forms\Answer::with('form', 'answerquestions')->where('id', $args['id'])->first();
    if (is_null($answer))
    {
      throw new \Exception('Id not found', 404);
    }

    if (is_null($answer->form))
    {
      throw new \Exception('Form not found', 404);
    }
    $form = \App\Models\Forms\Form::with('sections')->where('id', $answer->form->id)->first();
    if (is_null($form))
    {
      throw new \Exception('Form not found', 404);
    }

    $answers = [
      'form_id'     => $answer->form->id,
      'form_name'   => $answer->form->name,
      'sections'    => [],
    ];

    $rootUrl = $this->genereRootUrl($request, '');
    // $rootUrl = $this->getUrlWithoutQuery($request);

    foreach ($form->sections as $section)
    {
      $answers['sections'][$section->id] = [];
      $answers['sections'][$section->id]['id'] = $section->id;
      $answers['sections'][$section->id]['name'] = $section->name;
      $answers['sections'][$section->id]['questions'] = [];

      $section = \App\Models\Forms\Section::with('questions')->where('id', $section->id)->first();
      if (!is_null($section))
      {
        foreach ($section->questions as $question)
        {
          $myAnswer = '';
          foreach ($answer->answerquestions as $answerquestion)
          {
            if ($answerquestion->question_id ==  $question->id)
            {
              $myAnswer = $answerquestion->answer;
              break;
            }
          }

          $answers['sections'][$section->id]['questions'][$question->id] = [
            'id'          => $question->id,
            'name'        => $question->name,
            'fieldtype'   => $question->fieldtype,
            'answer'      => $myAnswer,
          ];
        }
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($answer, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($answer));
    $viewData->addData('answers', $answers);
    $viewData->addData('src', 'answer');

    return $view->render($response, 'subitem/answers.html.twig', (array)$viewData);
  }
}
