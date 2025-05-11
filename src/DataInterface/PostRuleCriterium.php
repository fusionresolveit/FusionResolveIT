<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostRuleCriterium extends Post
{
  /** @var ?\App\Models\Rules\Rule */
  public $rule;

  /** @var ?string */
  public $criteria;

  /** @var ?int */
  public $condition;

  /** @var ?string */
  public $pattern;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Rules\Rule');
    $rulecrit = new \App\Models\Rules\Rulecriterium();
    $this->definitions = $rulecrit->getDefinitions(true);

    if (
        Validation::attrNumericVal('rule')->isValid($data) &&
        isset($data->rule)
    )
    {
      $rule = \App\Models\Rules\Rule::where('id', $data->rule)->first();
      if (!is_null($rule))
      {
        $this->rule = $rule;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrStr('criteria')->isValid($data) &&
        isset($data->criteria)
    )
    {
      $this->criteria = $data->criteria;
    }

    $conditions = \App\Models\Definitions\Rulecriterium::getConditionArray();
    if (
        Validation::attrNumericVal('condition')->isValid($data) &&
        isset($data->condition) &&
        isset($conditions[intval($data->condition)])
    )
    {
      $this->condition = intval($data->condition);
    } else {
      throw new \Exception('Wrong data request', 400);
    }

    if (
        Validation::attrStr('pattern')->isValid($data) &&
        isset($data->pattern)
    )
    {
      $this->pattern = $data->pattern;
    }
  }

  /**
   * @return array{rule?: \App\Models\Rules\Rule, criteria?: string, condition?: int, pattern?: string}
   */
  public function exportToArray(bool $filterRights = false): array
  {
    $vars = get_object_vars($this);
    $data = [];
    foreach (array_keys($vars) as $key)
    {
      if (!is_null($this->{$key}))
      {
        if (!$filterRights)
        {
          $this->getFieldForArray($key, $data);
        } else {
          // TODO filter by custom
          if (is_null($this->profileright))
          {
            return [];
          }
          elseif (count($this->profilerightcustoms) > 0)
          {
            foreach ($this->profilerightcustoms as $custom)
            {
              if ($custom->write)
              {
                $this->getFieldForArray($key, $data);
              }
            }
          } else {
            $this->getFieldForArray($key, $data);
          }
        }
      }
    }
    return $data;
  }

  /**
   * @param-out array{rule?: \App\Models\Rules\Rule, criteria?: string, condition?: int, pattern?: string} $data
   */
  private function getFieldForArray(string $key, mixed &$data): void
  {
    foreach ($this->definitions as $def)
    {
      if ($def->name == $key)
      {
        if (!is_null($def->dbname))
        {
          $data[$def->dbname] = $this->{$key}->id;
          return;
        }
        if ($def->multiple === true)
        {
          $data[$key] = [];
          foreach ($this->{$key} as $item)
          {
            $data[$key][] = $item->id;
          }
          return;
        }
        $data[$key] = $this->{$key};
        return;
      }
    }
  }
}
