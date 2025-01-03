<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Dropdown extends Common
{
  public function getAll(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getQueryParams();
    $dropData = [];
    $success = false;

    if (property_exists($data, 'itemtype') && class_exists($data->itemtype))
    {
      $item = new $data->itemtype();
      $dropData = $item->getDropdownValues($data->q);
      $success = true;
    }
    else
    {
      $dropData = [];
      $success = false;
    }

    $respdata = [
      "success" => $success,
      "results" => $dropData,
    ];

    $response->getBody()->write(json_encode($respdata));
    return $response->withHeader('Content-Type', 'application/json');
  }

  /**
   * Get the criteria for a rule
   */
  public function getRuleCriteria(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getQueryParams();

    $dropData = [];
    $success = false;

    $classname = '\\App\\Models\\' . $data->itemtype;
    $item = new $classname();

    // TODO manage usein = rule
    // TODO manage only fields have display = true

    $criteria = $item->getDefinitions();
    foreach ($criteria as $crit)
    {
      $dropData[] = [
        'name'  => $crit['title'],
        'value' => $crit['name'],
      ];
    }

    $respdata = [
      "success" => $success,
      "results" => $dropData,
    ];

    $response->getBody()->write(json_encode($respdata));
    return $response->withHeader('Content-Type', 'application/json');
  }

  /**
   * Get the condition rule, for the criteria selected
   */
  public function getRuleCriteriaCondition(Request $request, Response $response, $args): Response
  {
    global $translator;
    $data = (object) $request->getQueryParams();

    $conditions = \App\v1\Controllers\Rules\Criterium::getConditionsForDefinition(
      $data->itemtype,
      $data->definitionname
    );

    $dropData  = [];
    foreach ($conditions as $condition)
    {
      switch ($condition) {
        case \App\v1\Controllers\Rules\Common::PATTERN_IS:
          $dropData[] = [
            'name'  => $translator->translate('is'),
            'value' => $condition,
          ];
            break;

        case \App\v1\Controllers\Rules\Common::PATTERN_IS_NOT:
          $dropData[] = [
            'name'  => $translator->translate('is not'),
            'value' => $condition,
          ];
            break;

        case \App\v1\Controllers\Rules\Common::PATTERN_CONTAIN:
          $dropData[] = [
            'name'  => $translator->translate('contains'),
            'value' => $condition,
          ];
            break;

        case \App\v1\Controllers\Rules\Common::PATTERN_NOT_CONTAIN:
          $dropData[] = [
            'name'  => $translator->translate('does not contains'),
            'value' => $condition,
          ];
            break;

        case \App\v1\Controllers\Rules\Common::PATTERN_BEGIN:
          $dropData[] = [
            'name'  => $translator->translate('starting with'),
            'value' => $condition,
          ];
            break;

        case \App\v1\Controllers\Rules\Common::PATTERN_END:
          $dropData[] = [
            'name'  => $translator->translate('finished by'),
            'value' => $condition,
          ];
            break;

        case \App\v1\Controllers\Rules\Common::REGEX_MATCH:
          $dropData[] = [
            'name'  => $translator->translate('regular expression matches'),
            'value' => $condition,
          ];
            break;

        case \App\v1\Controllers\Rules\Common::REGEX_NOT_MATCH:
          $dropData[] = [
            'name'  => $translator->translate('regular expression does not match'),
            'value' => $condition,
          ];
            break;

        case \App\v1\Controllers\Rules\Common::PATTERN_EXISTS:
          $dropData[] = [
            'name'  => $translator->translate('exists'),
            'value' => $condition,
          ];
            break;

        case \App\v1\Controllers\Rules\Common::PATTERN_DOES_NOT_EXISTS:
          $dropData[] = [
            'name'  => $translator->translate('does not exist'),
            'value' => $condition,
          ];
            break;

        case \App\v1\Controllers\Rules\Common::PATTERN_UNDER:
          $dropData[] = [
            'name'  => $translator->translate('is under'),
            'value' => $condition,
          ];
            break;

        case \App\v1\Controllers\Rules\Common::PATTERN_NOT_UNDER:
          $dropData[] = [
            'name'  => $translator->translate('is not under'),
            'value' => $condition,
          ];
            break;

        case \App\v1\Controllers\Rules\Common::PATTERN_IS_EMPTY:
          $dropData[] = [
            'name'  => $translator->translate('is empty'),
            'value' => $condition,
          ];
            break;
      }
    }

    $respdata = [
      "success" => true,
      "results" => $dropData,
    ];

    $response->getBody()->write(json_encode($respdata));
    return $response->withHeader('Content-Type', 'application/json');
  }

  /**
   * Get the rule pattern, based on criteria and condition
   */
  public function getRuleCriteriaPattern(Request $request, Response $response, $args): Response
  {
    global $translator;

    $data = (object) $request->getQueryParams();
    // itemtype
    // definitionname
    // condition

    $returnData = [];
    switch ($data->condition) {
      case \App\v1\Controllers\Rules\Common::PATTERN_IS:
      case \App\v1\Controllers\Rules\Common::PATTERN_IS_NOT:
      case \App\v1\Controllers\Rules\Common::PATTERN_UNDER:
      case \App\v1\Controllers\Rules\Common::PATTERN_NOT_UNDER:
        $completeModelName = '\App\Models\\' . $data->itemtype;
        if (!class_exists($completeModelName))
        {
          return $this->returnNoSuccess($response);
        }
        $itemCriteria = new $completeModelName();

        $definitions = $itemCriteria->getDefinitions();
        foreach ($definitions as $definition)
        {
          if ($definition['name'] == $data->definitionname)
          {
            if (isset($definition['values']))
            {
              $valuesForDrodown = [];
              foreach ($definition['values'] as $key => $value)
              {
                $valuesForDrodown[] = [
                  'name'  => $value['title'],
                  'value' => $key,
                ];
              }

              $respdata = [
                "success" => true,
                "results" => $valuesForDrodown,
              ];
              $response->getBody()->write(json_encode($respdata));
              return $response->withHeader('Content-Type', 'application/json');
            }
            $item = new $definition['itemtype']();
            $returnData = $item->getDropdownValues($data->q);
            $respdata = [
              "success" => true,
              "results" => $returnData,
            ];
            $response->getBody()->write(json_encode($respdata));
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
          break;

      case \App\v1\Controllers\Rules\Common::PATTERN_EXISTS:
      case \App\v1\Controllers\Rules\Common::PATTERN_DOES_NOT_EXISTS:
      case \App\v1\Controllers\Rules\Common::PATTERN_IS_EMPTY:
        $returnData[] = [
          'name'  => $translator->translate('yes'),
          'value' => true,
        ];
          break;
    }
    $respdata = [
      "success" => true,
      "results" => $returnData,
    ];
    $response->getBody()->write(json_encode($respdata));
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function getRuleActionsField(Request $request, Response $response, $args): Response
  {
    return $this->getRuleCriteria($request, $response, $args);
  }

  public function getRuleActionsType(Request $request, Response $response, $args): Response
  {
    global $translator;

    $data = (object) $request->getQueryParams();
    $completeModelName = '\App\Models\\' . $data->itemtype;
    if (!class_exists($completeModelName))
    {
      return $this->returnNoSuccess($response);
    }
    $item = new $completeModelName();
    $definitions = $item->getDefinitions();
    $types = [];
    foreach ($definitions as $definition)
    {
      if ($definition['name'] == $data->definitionname)
      {
        switch ($definition['type']) {
          case 'dropdown_remote':
          case 'dropdown':
          case 'boolean':
            $types = [
              [
                'name'  => $translator->translate('assign'),
                'value' => 'assign_dropdown'
              ]
            ];
            if (isset($definition['multiple']))
            {
              $types[] = [
                'name'  => $translator->translate('append'),
                'value' => 'append_dropdown'
              ];
            }
              break;

          case 'input':
          case 'textarea':
          case 'date':
          case 'datetime':
            $types = [
              [
                'name'  => $translator->translate('assign'),
                'value' => 'assign'
              ],
              [
                'name'  => $translator->translate('append'),
                'value' => 'append'
              ],
              [
                'name'  => $translator->translate('assign regex result'),
                'value' => 'regex_result'
              ],
              [
                'name'  => $translator->translate('append regex result'),
                'value' => 'append_regex_result'
              ]
            ];
              break;
        }
        break;
      }
    }
    $respdata = [
      "success" => true,
      "results" => $types,
    ];
    $response->getBody()->write(json_encode($respdata));
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function getRuleActionsValue(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getQueryParams();
    $completeModelName = '\App\Models\\' . $data->itemtype;
    if (!class_exists($completeModelName))
    {
      return $this->returnNoSuccess($response);
    }
    $respdata = [];
    $dropData = [];
    $success = false;

    $item = new $completeModelName();
    $definitions = $item->getDefinitions();

    foreach ($definitions as $definition)
    {
      if ($definition['name'] == $data->field)
      {
        if (class_exists($definition['itemtype']))
        {
          $item = new $definition['itemtype']();
          $dropData = $item->getDropdownValues($data->q);
          $success = true;
        }
        break;
      }
    }
    $respdata = [
      "success" => $success,
      "results" => $dropData,
    ];
    $response->getBody()->write(json_encode($respdata));
    return $response->withHeader('Content-Type', 'application/json');
  }

  private function returnNoSuccess($response)
  {
    $respdata = [
      "success" => false,
      "results" => [],
    ];
    $response->getBody()->write(json_encode($respdata));
    return $response->withHeader('Content-Type', 'application/json');
  }
}
