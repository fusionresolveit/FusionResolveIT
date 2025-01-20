<?php

declare(strict_types=1);

namespace App\v1\Controllers\Rules;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Ticket extends Common
{
  protected $use_output_rule_process_as_next_input = true;
  protected $criteriaDefinitionModel = '\App\v1\Controllers\Rules\Criteria\Ticket';
  protected $actionsDefinitionModel = '\App\v1\Controllers\Rules\Actions\Ticket';
  protected $model = '\App\Models\Rules\Ticket';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Rules\Ticket();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Rules\Ticket();
    $view = Twig::fromRequest($request);

    // Load the item
    $myItem = $item->find($args['id']);

    $rootUrl = $this->getUrlWithoutQuery($request);

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addHeaderColor('red');

    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));

    return $view->render($response, 'genericForm.html.twig', (array)$viewData);
  }

  public function showCriteria(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Rules\Ticket();
    $view = Twig::fromRequest($request);

    // Load the item
    $myItem = $item->find($args['id']);

    $rulecriteria = \App\Models\Rules\Rulecriterium::
        where('rule_id', $myItem->id)
      ->get();
    $list = [];
    foreach ($rulecriteria as $rc)
    {
      $condition = \App\v1\Controllers\Rules\Criterium::getConditionForCriterium($rc->criteria, $rc->condition);
      $condition['readonly'] = true;
      $patternviewfield = $rc->patternviewfield;
      $patternviewfield['readonly'] = true;
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
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addHeaderColor('red');

    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('criteria', $list);

    $viewData->addData('model', 'Ticket');

    return $view->render($response, 'subitem/Rules/rulecriteria.html.twig', (array)$viewData);
  }

  public function showActions(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Rules\Ticket();
    $view = Twig::fromRequest($request);

    // Load the item
    $myItem = $item->find($args['id']);

    $ruleActions = \App\Models\Rules\Ruleaction::
        where('rule_id', $myItem->id)
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
        $fields['values'][$definition['name']] = [
          'title' => $definition['title'],
        ];
        if ($definition['name'] == $ra->field)
        {
          $fields['value'] = $ra->field;
          $fields['valuename'] = $definition['title'];
        }
      }

      $value = $ra->valueviewfield;
      $value['readonly'] = true;
      $value['title'] = 'Value';
      $list[] = [
        'id'          => $ra->id,
        'action_type' => $ra->action_type,
        'field'       => $fields,
        'value'       => $value,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/actions');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addHeaderColor('red');

    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('actions', $list);

    return $view->render($response, 'subitem/Rules/ruleactions.html.twig', (array)$viewData);
  }

  public function showNewCriteria(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Rules\Ticket();
    $view = Twig::fromRequest($request);

    // Load the item
    $myItem = $item->find($args['id']);

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

  public function newCriteria(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getParsedBody();

    $item = new \App\Models\Rules\Rulecriterium();
    $item->rule_id = $args['id'];
    $item->criteria = $data->criteria;
    $item->condition = $data->condition;
    $item->pattern = $data->pattern;
    $item->save();

    // add message to session
    \App\v1\Controllers\Toolbox::addSessionMessage('The criterion has been created successfully');

    $uri = $request->getUri();
    $newUrl = rtrim((string) $uri, '/new');

    return $response
      ->withHeader('Location', $newUrl);
  }

  public function showNewAction(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Rules\Ticket();
    $view = Twig::fromRequest($request);

    // Load the item
    $myItem = $item->find($args['id']);

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

  public function newAction(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getParsedBody();

    $item = new \App\Models\Rules\Ruleaction();
    $item->rule_id = $args['id'];
    $item->action_type = $data->actiontype;
    $item->field = $data->field;
    $item->value = $data->value;
    $item->save();

    // add message to session
    \App\v1\Controllers\Toolbox::addSessionMessage('The action has been created successfully');

    $uri = $request->getUri();
    $newUrl = rtrim((string) $uri, '/new');
    return $response
      ->withHeader('Location', $newUrl);
  }
}
