<?php

declare(strict_types=1);

namespace App\Traits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use stdClass;

trait ProcessRules
{
  /** @var bool */
  protected $stop_on_first_match = false;

  /** @var array<string> */
  public $regex_results = [];

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

  /**
   * @template D of \App\DataInterface\Post
   * @param D $data
   *
   * @return D
   */
  public function processAllRules($data)
  {
    // Get rules
    $rule = $this->instanciateModel();
    $rules = $rule->where('is_active', true)->get();

    if (count($rules))
    {
      foreach ($rules as $rule)
      {
        $data = $this->process($rule, $data);
      }
    }
    return $data;
  }

  /**
   * @template C of \App\Models\Rules\Rule
   * @template D of \App\DataInterface\Post
   * @param C $rule
   * @param D $data
   *
   * @return D
   */
  public function process($rule, $data)
  {
    // if ($this->validateCriterias($options))
    // {
    $this->regex_results     = [];
    // $input = $this->prepareInputDataForProcess($input, $params);

    if ($this->checkCriterias($rule, $data))
    {
      $data = $this->executeActions($rule, $data);
    }
    return $data;
  }

  /**
   * @template C of \App\Models\Rules\Rule
   * @template D of \App\DataInterface\Post
   * @param C $rule
   * @param D $data
   *
   * @return D
   */
  public function executeActions($rule, $data)
  {
    $actions = \App\Models\Rules\Ruleaction::
        where('rule_id', $rule->id)
      ->get();

    if (count($actions))
    {
      foreach ($actions as $action)
      {
        $act = new \App\v1\Controllers\Rules\Action();
        $data = $act->runAction($action, $data, $this->regex_results);
      }
    }
    return $data;
  }

  /**
   * Check criteria
   *
   * @template C of \App\Models\Rules\Rule
   * @template D of \App\DataInterface\Post
   * @param C $rule
   * @param D $data
   */
  public function checkCriterias($rule, $data): bool
  {
    if (is_null($rule->match))
    {
      throw new \Exception('Error in rule', 500);
    }
    $criteria = \App\Models\Rules\Rulecriterium::
        where('rule_id', $rule->id)
      ->get();

    foreach ($criteria as $criterium)
    {
      $crit = new \App\v1\Controllers\Rules\Criterium();
      $ret = $crit->checkCriteria($criterium, $data);
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
   * @param array<mixed> $input
   */
  public function findWithGlobalCriteria($input): bool
  {
    return true;
  }

  /**
   * Prepare data for the rules
   *
   * @template C of \App\Models\Common
   * @template D of \App\DataInterface\Post
   * @param C $item
   * @param D $data
   *
   * @return D
   */
  public function prepareData($item, $data)
  {
    $definitions = $item->getDefinitions(true);
    $filledFields = $data->getFilledFields();

    foreach ($definitions as $def)
    {
      if ($def->readonly || !$def->fillable)
      {
        continue;
      }
      if (!in_array($def->name, $filledFields))
      {
        $data->{$def->name} = $item->{$def->name};
      }
    }
    return $data;
  }
}
