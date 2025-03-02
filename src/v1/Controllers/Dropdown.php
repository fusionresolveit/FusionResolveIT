<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Dropdown extends Common
{
  /**
   * @param array<string, string> $args
   */
  public function getAll(Request $request, Response $response, array $args): Response
  {
    $data = (object) $request->getQueryParams();
    $dropData = [];
    $success = false;

    if (property_exists($data, 'itemtype') && class_exists($data->itemtype))
    {
      $item = new $data->itemtype();
      if (!method_exists($item, 'getDropdownValues'))
      {
        throw new \Exception('Error', 500);
      }
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

    $json = json_encode($respdata);
    if ($json === false)
    {
      $response->getBody()->write('[]');
    } else {
      $response->getBody()->write($json);
    }
    return $response->withHeader('Content-Type', 'application/json');
  }

  /**
   * Get the criteria for a rule
   *
   * @param array<string, string> $args
   */
  public function getRuleCriteria(Request $request, Response $response, array $args): Response
  {
    $data = (object) $request->getQueryParams();

    $dropData = [];
    $success = false;

    $classname = '\\App\\Models\\' . $data->itemtype;
    $item = new $classname();

    // TODO manage usein = rule
    // TODO manage only fields have display = true

    if (!method_exists($item, 'getDefinitions'))
    {
      throw new \Exception('Error', 500);
    }

    $criteria = $item->getDefinitions();
    foreach ($criteria as $crit)
    {
      $dropData[] = [
        'name'  => $crit->title,
        'value' => $crit->name,
      ];
    }

    $respdata = [
      "success" => $success,
      "results" => $dropData,
    ];

    $json = json_encode($respdata);
    if ($json === false)
    {
      $response->getBody()->write('[]');
    } else {
      $response->getBody()->write($json);
    }
    return $response->withHeader('Content-Type', 'application/json');
  }

  /**
   * Get the condition rule, for the criteria selected
   *
   * @param array<string, string> $args
   */
  public function getRuleCriteriaCondition(Request $request, Response $response, array $args): Response
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
      switch ($condition)
      {
        case 0: // Pattern is
          $dropData[] = [
            'name'  => $translator->translate('is'),
            'value' => $condition,
          ];
            break;

        case 1: // Pattern is not
          $dropData[] = [
            'name'  => $translator->translate('is not'),
            'value' => $condition,
          ];
            break;

        case 2: // Pattern contain
          $dropData[] = [
            'name'  => $translator->translate('contains'),
            'value' => $condition,
          ];
            break;

        case 3: // Pattern not contain
          $dropData[] = [
            'name'  => $translator->translate('does not contains'),
            'value' => $condition,
          ];
            break;

        case 4: // Pattern begin
          $dropData[] = [
            'name'  => $translator->translate('starting with'),
            'value' => $condition,
          ];
            break;

        case 5: // Pattern end
          $dropData[] = [
            'name'  => $translator->translate('finished by'),
            'value' => $condition,
          ];
            break;

        case 6: // Regex match
          $dropData[] = [
            'name'  => $translator->translate('regular expression matches'),
            'value' => $condition,
          ];
            break;

        case 7: // Regex not match
          $dropData[] = [
            'name'  => $translator->translate('regular expression does not match'),
            'value' => $condition,
          ];
            break;

        case 8: // Pattern exists
          $dropData[] = [
            'name'  => $translator->translate('exists'),
            'value' => $condition,
          ];
            break;

        case 9: // Pattern not exists
          $dropData[] = [
            'name'  => $translator->translate('does not exist'),
            'value' => $condition,
          ];
            break;

        case 11: // Pattern under
          $dropData[] = [
            'name'  => $translator->translate('is under'),
            'value' => $condition,
          ];
            break;

        case 12: // Pattern not under
          $dropData[] = [
            'name'  => $translator->translate('is not under'),
            'value' => $condition,
          ];
            break;

        case 30: // Pattern is empty
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

    $json = json_encode($respdata);
    if ($json === false)
    {
      $response->getBody()->write('[]');
    } else {
      $response->getBody()->write($json);
    }
    return $response->withHeader('Content-Type', 'application/json');
  }

  /**
   * Get the rule pattern, based on criteria and condition
   *
   * @param array<string, string> $args
   */
  public function getRuleCriteriaPattern(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $data = (object) $request->getQueryParams();
    // itemtype
    // definitionname
    // condition

    $returnData = [];
    switch ($data->condition)
    {
      case 0: // Pattern is
      case 1: // Pattern is not
      case 11: // Pattern under
      case 12: // Pattern not under
        $completeModelName = '\App\Models\\' . $data->itemtype;
        if (!class_exists($completeModelName))
        {
          return $this->returnNoSuccess($response);
        }
        $itemCriteria = new $completeModelName();

        if (!method_exists($itemCriteria, 'getDefinitions'))
        {
          throw new \Exception('Error', 500);
        }

        $definitions = $itemCriteria->getDefinitions();
        foreach ($definitions as $definition)
        {
          if ($definition->name == $data->definitionname)
          {
            if (!is_null($definition->values))
            {
              $valuesForDrodown = [];
              foreach ($definition->values as $key => $value)
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
              $json = json_encode($respdata);
              if ($json === false)
              {
                $response->getBody()->write('[]');
              } else {
                $response->getBody()->write($json);
              }

              return $response->withHeader('Content-Type', 'application/json');
            }
            $item = new $definition->itemtype();
            if (!method_exists($item, 'getDropdownValues'))
            {
              throw new \Exception('Error', 500);
            }

            $returnData = $item->getDropdownValues($data->q);
            $respdata = [
              "success" => true,
              "results" => $returnData,
            ];
            $json = json_encode($respdata);
            if ($json === false)
            {
              $response->getBody()->write('[]');
            } else {
              $response->getBody()->write($json);
            }
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
          break;

      case 8: // Pattern exists
      case 9: // Pattern not exists
      case 30: // Pattern is empty
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

    $json = json_encode($respdata);
    if ($json === false)
    {
      $response->getBody()->write('[]');
    } else {
      $response->getBody()->write($json);
    }
    return $response->withHeader('Content-Type', 'application/json');
  }

  /**
   * @param array<string, string> $args
   */
  public function getRuleActionsField(Request $request, Response $response, array $args): Response
  {
    return $this->getRuleCriteria($request, $response, $args);
  }

  /**
   * @param array<string, string> $args
   */
  public function getRuleActionsType(Request $request, Response $response, array $args): Response
  {
    global $translator;

    $data = (object) $request->getQueryParams();
    $completeModelName = '\App\Models\\' . $data->itemtype;
    if (!class_exists($completeModelName))
    {
      return $this->returnNoSuccess($response);
    }
    $item = new $completeModelName();
    if (!method_exists($item, 'getDefinitions'))
    {
      throw new \Exception('Error', 500);
    }

    $definitions = $item->getDefinitions();
    $types = [];
    foreach ($definitions as $definition)
    {
      if ($definition->name == $data->definitionname)
      {
        switch ($definition->type)
        {
          case 'dropdown_remote':
          case 'dropdown':
          case 'boolean':
            $types = [
              [
                'name'  => $translator->translate('assign'),
                'value' => 'assign_dropdown'
              ]
            ];
            if (!is_null($definition->multiple) && $definition->multiple)
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

    $json = json_encode($respdata);
    if ($json === false)
    {
      $response->getBody()->write('[]');
    } else {
      $response->getBody()->write($json);
    }
    return $response->withHeader('Content-Type', 'application/json');
  }

  /**
   * @param array<string, string> $args
   */
  public function getRuleActionsValue(Request $request, Response $response, array $args): Response
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
    if (!method_exists($item, 'getDefinitions'))
    {
      throw new \Exception('Error', 500);
    }
    $definitions = $item->getDefinitions();

    foreach ($definitions as $definition)
    {
      if ($definition->name == $data->field)
      {
        if (class_exists($definition->itemtype))
        {
          $item = new $definition->itemtype();
          if (!method_exists($item, 'getDropdownValues'))
          {
            throw new \Exception('Error', 500);
          }
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

    $json = json_encode($respdata);
    if ($json === false)
    {
      $response->getBody()->write('[]');
    } else {
      $response->getBody()->write($json);
    }
    return $response->withHeader('Content-Type', 'application/json');
  }

  private function returnNoSuccess(Response $response): Response
  {
    $respdata = [
      "success" => false,
      "results" => [],
    ];

    $json = json_encode($respdata);
    if ($json === false)
    {
      $response->getBody()->write('[]');
    } else {
      $response->getBody()->write($json);
    }
    return $response->withHeader('Content-Type', 'application/json');
  }

  /**
   * @param array<int, string> $toadd
   *
   * @return array<mixed>
   */
  public static function generateNumbers(int $min, int $max, int $step = 1, array $toadd = [], string $unit = ''): array
  {
    $dropdown = new self();
    $tab = [];
    foreach (array_keys($toadd) as $key)
    {
      $tab[$key]['title'] = $toadd[$key];
    }

    for ($i = $min; $i <= $max; $i = $i + $step)
    {
      $tab[$i]['title'] = $dropdown->getValueWithUnit($i, $unit, 0);
    }
    return $tab;
  }
}
