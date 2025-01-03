<?php

declare(strict_types=1);

namespace App\v1\Controllers\Rules;

use stdClass;

class Common extends \App\v1\Controllers\Common
{
  protected $stop_on_first_match = false;

  public $regex_results = [];

  protected $criteriaDefinitionModel = null;
  protected $actionsDefinitionModel = null;

  public const RULE_WILDCARD           = '*';

  //Generic rules engine
  public const PATTERN_IS              = 0;
  public const PATTERN_IS_NOT          = 1;
  public const PATTERN_CONTAIN         = 2;
  public const PATTERN_NOT_CONTAIN     = 3;
  public const PATTERN_BEGIN           = 4;
  public const PATTERN_END             = 5;
  public const REGEX_MATCH             = 6;
  public const REGEX_NOT_MATCH         = 7;
  public const PATTERN_EXISTS          = 8;
  public const PATTERN_DOES_NOT_EXISTS = 9;
  public const PATTERN_FIND            = 10; // Global criteria
  public const PATTERN_UNDER           = 11;
  public const PATTERN_NOT_UNDER       = 12;
  public const PATTERN_IS_EMPTY        = 30; // Global criteria

  public const AND_MATCHING            = 'AND';
  public const OR_MATCHING             = 'OR';


  public function processAllRules(\App\Models\Common $item, $preparedData)
  {
    // Get rules
    $rules = $this->model::where('is_active', true)->get();

    if (count($rules))
    {
      foreach ($rules as $rule)
      {
        $preparedData = $this->process($rule, $item, $preparedData);
      }
    }
    return $preparedData;
  }

  public function process($rule, $item, $preparedData)
  {
    // if ($this->validateCriterias($options))
    // {
    $this->regex_results     = [];
    // $input = $this->prepareInputDataForProcess($input, $params);

    if ($this->checkCriterias($rule, $item, $preparedData))
    {
      $preparedData = $this->executeActions($rule, $item, $preparedData);
    }
    return $preparedData;
  }

  public function executeActions($rule, $item, $preparedData)
  {
    $actions = \App\Models\Rules\Ruleaction::
        where('rule_id', $rule->id)
      ->get();

    if (count($actions))
    {
      foreach ($actions as $action)
      {
        $act = new \App\v1\Controllers\Rules\Action();
        $preparedData = $act->runAction($action, $preparedData, $this->regex_results);
      }
    }
    return $preparedData;
  }

  /**
   * Check criteria
  **/
  public function checkCriterias($rule, $item, $preparedData): bool
  {
    $criteria = \App\Models\Rules\Rulecriterium::
        where('rule_id', $rule->id)
      ->get();

    foreach ($criteria as $criterium)
    {
      $crit = new \App\v1\Controllers\Rules\Criterium();
      $ret = $crit->checkCriteria($criterium, $preparedData);
      if (trim($rule->match) == self::AND_MATCHING && $ret === false)
      {
        return false;
      }
      elseif (trim($rule->match) == self::OR_MATCHING && $ret === true)
      {
        return true;
      }
      if (empty($this->regex_results))
      {
        $this->regex_results = $crit->getRegexResults();
      }
    }
    // We are here because it's validated
    return true;
  }

  /**
   * @param $input
  **/
  public function findWithGlobalCriteria($input)
  {
    return true;
  }

  /**
   * Prepare data for the rules
   *
   * @return array
   */
  public function prepareData(\App\Models\Common $item, stdClass $data)
  {
    $ruleData = [];
    $definitions = $item->getDefinitions();
    foreach ($definitions as $def)
    {
      if (isset($def['readonly']))
      {
        continue;
      }
      $key = $def['name'];
      if ($def['type'] == 'dropdown' || $def['type'] == 'dropdown_remote')
      {
        if (isset($def['multiple']))
        {
          if (property_exists($data, $key))
          {
            if (empty($data->{$key}))
            {
              $ruleData[$key] = [];
            } else {
              $ruleData[$key] = explode(',', $data->{$key});
            }
          } else {
            $ruleData[$key] = [];
            foreach ($item->{$key} as $relItem)
            {
              $ruleData[$key][] = $relItem->id;
            }
          }
        } else {
          if (property_exists($data, $key))
          {
            $ruleData[$key] = $data->{$key};
          } elseif (property_exists($item, $key)) {
            $ruleData[$key] = $item->{$key}['id'];
          }
        }
      } else {
        if (property_exists($data, $key))
        {
          $ruleData[$key] = $data->{$key};
        } else {
          $ruleData[$key] = $item->{$key};
        }
      }
    }
    return $ruleData;
  }

  public function parseNewData(\App\Models\Common $item, $data, $ruledData)
  {
    $definitions = $item->getDefinitions(false, 'rule');
    foreach ($definitions as $def)
    {
      if (!isset($ruledData[$def['name']]))
      {
        continue;
      }
      if (property_exists($data, $def['name']) && $ruledData[$def['name']] === $data->{$def['name']})
      {
        continue;
      }
      if (isset($def['multiple']))
      {
        $data->{$def['name']} = implode(',', $ruledData[$def['name']]);
      } else {
        $data->{$def['name']} = $ruledData[$def['name']];
      }
    }
    return $data;
  }
}
