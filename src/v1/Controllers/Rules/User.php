<?php

declare(strict_types=1);

namespace App\v1\Controllers\Rules;

use App\DataInterface\PostRuleAction;
use App\DataInterface\PostRuleCriterium;
use App\DataInterface\PostRuleRight;
use App\Traits\ProcessRules;
use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\ShowNewItem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class User extends \App\v1\Controllers\Common
{
  // Rules
  use ProcessRules;

  // Display
  use ShowItem;
  use ShowNewItem;
  use ShowAll;

  /** @var string */
  protected $criteriaDefinitionModel = '\App\v1\Controllers\Rules\Criteria\Right';

  /** @var string */
  protected $actionsDefinitionModel = '\App\v1\Controllers\Rules\Actions\Right';

  protected $model = \App\Models\Rules\User::class;

  protected function instanciateModel(): \App\Models\Rules\User
  {
    return new \App\Models\Rules\User();
  }

  /**
   * @param array<string, string> $args
   */
  public function newItem(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new PostRuleRight((object) $request->getParsedBody());

    $rule = new \App\Models\Rules\User();

    if (!$this->canRightCreate())
    {
      throw new \Exception('Unauthorized access', 401);
    }

    if (!\App\v1\Controllers\Profile::canRightReadItem($rule))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $rule = \App\Models\Rules\User::create($data->exportToArray());

    \App\v1\Controllers\Toolbox::addSessionMessage('The right rule has been created successfully');
    \App\v1\Controllers\Notification::prepareNotification($rule, 'new');

    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', $basePath . '/view/rules/rights/' . $rule->id)
        ->withStatus(302);
    }

    return $response
      ->withHeader('Location', $basePath . '/view/rules/rights')
      ->withStatus(302);
  }

  // /**
  //  * @param array<string, string> $args
  //  */
  // public function showItem(Request $request, Response $response, array $args): Response
  // {
  //   $item = new \App\Models\Rules\Ticket();
  //   $view = Twig::fromRequest($request);

  //   // Load the item
  //   $myItem = $item->where('id', $args['id'])->first();
  //   if (is_null($myItem))
  //   {
  //     throw new \Exception('Id not found', 404);
  //   }

  //   $rootUrl = $this->getUrlWithoutQuery($request);

  //   // form data
  //   $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
  //   $viewData->addHeaderColor('red');

  //   $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

  //   $viewData->addData('fields', $item->getFormData($myItem));

  //   return $view->render($response, 'genericForm.html.twig', (array)$viewData);
  // }

  /**
   * @param array<string, string> $args
   */
  public function showCriteria(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Rules\User();
    $view = Twig::fromRequest($request);

    // Load the item
    $ruleRight = \App\Models\Rules\User::where('id', $args['id'])->first();
    if (is_null($ruleRight))
    {
      throw new \Exception('Id not found', 404);
    }

    $rulecriteria = \App\Models\Rules\Rulecriterium::
        where('rule_id', $ruleRight->id)
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
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($ruleRight, $request);
    $viewData->addHeaderColor('red');

    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($ruleRight));
    $viewData->addData('criteria', $list);
    $viewData->addData('rooturl', $rootUrl);

    $viewData->addData('model', 'User');

    return $view->render($response, 'subitem/Rules/rulecriteria.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showActions(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Rules\User();
    $view = Twig::fromRequest($request);

    // Load the item
    $ruleUser = \App\Models\Rules\User::where('id', $args['id'])->first();
    if (is_null($ruleUser))
    {
      throw new \Exception('Id not found', 404);
    }

    $ruleActions = \App\Models\Rules\Ruleaction::
        where('rule_id', $ruleUser->id)
      ->get();
    $list = [];
    $user = new \App\Models\User();
    $definitions = $user->getDefinitions();
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
        'action_type' => \App\Models\Definitions\Ruleaction::getActiontypeArray()[$ra->action_type]['title'],
        'field'       => $fields,
        'value'       => $value,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/actions');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($ruleUser, $request);
    $viewData->addHeaderColor('red');

    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($ruleUser));
    $viewData->addData('actions', $list);
    $viewData->addData('rooturl', $rootUrl);

    return $view->render($response, 'subitem/Rules/ruleactions.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function showNewCriteria(Request $request, Response $response, array $args): Response
  {
    $item = new \App\Models\Rules\User();
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

    $viewData->addData('model', 'User');

    return $view->render($response, 'subitem/Rules/newcriteria.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function newCriteria(Request $request, Response $response, array $args): Response
  {
    $dataRequest = (array) $request->getParsedBody();
    $dataRequest['rule'] = $args['id'];
    $data = new PostRuleCriterium((object) $dataRequest);

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
    $item = new \App\Models\Rules\User();
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

    $viewData->addData('model', 'User');

    return $view->render($response, 'subitem/Rules/newaction.html.twig', (array)$viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function newAction(Request $request, Response $response, array $args): Response
  {
    $dataRequest = (array) $request->getParsedBody();
    $dataRequest['rule'] = $args['id'];
    $data = new PostRuleAction((object) $dataRequest);

    \App\Models\Rules\Ruleaction::create($data->exportToArray());

    // add message to session
    \App\v1\Controllers\Toolbox::addSessionMessage('The action has been created successfully');

    $uri = $request->getUri();
    $newUrl = rtrim((string) $uri, '/new');
    return $response
      ->withHeader('Location', $newUrl);
  }
}
