<?php

declare(strict_types=1);

namespace App\v1\Controllers\Rules;

use App\DataInterface\PostRuleAction;
use App\DataInterface\PostRuleCriterium;
use App\Traits\ProcessRules;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Ticket extends \App\v1\Controllers\Common
{
  // Rules
  use ProcessRules;

  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  /** @var string */
  protected $criteriaDefinitionModel = '\App\v1\Controllers\Rules\Criteria\Ticket';

  /** @var string */
  protected $actionsDefinitionModel = '\App\v1\Controllers\Rules\Actions\Ticket';

  protected $model = \App\Models\Rules\Ticket::class;

  protected function instanciateModel(): \App\Models\Rules\Ticket
  {
    return new \App\Models\Rules\Ticket();
  }

  /**
   * @param array<string, string> $args
   */
  public function showItem(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Rules\Ticket();
    $view = Twig::fromRequest($request);

    // Load the item
    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->getUrlWithoutQuery($request);

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addHeaderColor('red');

    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));

    return $view->render($response, 'genericForm.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showCriteria(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Rules\Ticket();
    $view = Twig::fromRequest($request);

    // Load the item
    $ruleTicket = \App\Models\Rules\Ticket::where('id', $args['id'])->first();
    if (is_null($ruleTicket))
    {
      throw new \Exception('Id not found', 404);
    }

    $rulecriteria = \App\Models\Rules\Rulecriterium::
        where('rule_id', $ruleTicket->id)
      ->get();
    $list = [];
    foreach ($rulecriteria as $rc)
    {
      $condition = \App\v1\Controllers\Rules\Criterium::getConditionForCriterium($rc->criteria, $rc->condition);
      $condition['readonly'] = true;
      $patternviewfield = $rc->patternviewfield;
      $patternviewfield->readonly = true;
      $list[] = [
        'id'        => $rc->id,
        'criteria'  => $rc->criteria,
        'condition' => $condition,
        'pattern'   => $rc->pattern,
        'patternviewfield' => $patternviewfield,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/criteria');

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($ruleTicket, $request);
    $viewData->addHeaderColor('red');

    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($ruleTicket));
    $viewData->addData('criteria', $list);

    $viewData->addData('model', 'Ticket');

    return $view->render($response, 'subitem/Rules/rulecriteria.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showActions(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Rules\Ticket();
    $view = Twig::fromRequest($request);

    // Load the item
    $ruleTicket = \App\Models\Rules\Ticket::where('id', $args['id'])->first();
    if (is_null($ruleTicket))
    {
      throw new \Exception('Id not found', 404);
    }

    $ruleActions = \App\Models\Rules\Ruleaction::
        where('rule_id', $ruleTicket->id)
      ->get();
    $list = [];
    $ticket = new \App\Models\Ticket();
    $definitions = $ticket->getDefinitions();
    foreach ($ruleActions as $ra)
    {
      $fields = [
        'title'   => 'Field',
        'type'    => 'dropdown',
        'name'    => 'value',
        'values'  => [],
        'readonly' => true,
      ];
      foreach ($definitions as $definition)
      {
        $fields['values'][$definition->name] = [
          'title' => $definition->title,
        ];
        if ($definition->name == $ra->field)
        {
          $fields['value'] = $ra->field;
          $fields['valuename'] = $definition->title;
        }
      }

      $value = $ra->valueviewfield;
      $value->readonly = true;
      $value->title = 'Value';
      $list[] = [
        'id'          => $ra->id,
        'action_type' => $ra->action_type,
        'field'       => $fields,
        'value'       => $value,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/actions');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($ruleTicket, $request);
    $viewData->addHeaderColor('red');

    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($ruleTicket));
    $viewData->addData('actions', $list);

    return $view->render($response, 'subitem/Rules/ruleactions.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showNewCriteria(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Rules\Ticket();
    $view = Twig::fromRequest($request);

    // Load the item
    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/criteria/new');

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addHeaderColor('red');

    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));

    $viewData->addData('model', 'Ticket');

    return $view->render($response, 'subitem/Rules/newcriteria.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function newCriteria(Request $request, Response $response, array $args): Response
  {
    $data = new PostRuleCriterium((object) $request->getParsedBody());

    \App\Models\Rules\Rulecriterium::create($data->exportToArray());

    // add message to session
    \App\v1\Controllers\Toolbox::addSessionMessage('The criterion has been created successfully');

    $uri = $request->getUri();
    $newUrl = rtrim((string) $uri, '/new');

    return $response
      ->withHeader('Location', $newUrl);
  }

  /**
   * @param array<string, string> $args
   */
  public function showNewAction(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Rules\Ticket();
    $view = Twig::fromRequest($request);

    // Load the item
    $myItem = $item->where('id', $args['id'])->first();
    if (is_null($myItem))
    {
      throw new \Exception('Id not found', 404);
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/actions/new');

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addHeaderColor('red');

    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));

    $viewData->addData('model', 'Ticket');

    return $view->render($response, 'subitem/Rules/newaction.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function newAction(Request $request, Response $response, array $args): Response
  {
    $data = new PostRuleAction((object) $request->getParsedBody());

    \App\Models\Rules\Ruleaction::create($data->exportToArray());

    // add message to session
    \App\v1\Controllers\Toolbox::addSessionMessage('The action has been created successfully');

    $uri = $request->getUri();
    $newUrl = rtrim((string) $uri, '/new');
    return $response
      ->withHeader('Location', $newUrl);
  }
}
