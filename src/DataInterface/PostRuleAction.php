<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostRuleAction extends Post
{
  /** @var ?\App\Models\Rules\Rule */
  public $rule;

  /** @var ?int */
  public $action_type;

  /** @var ?string */
  public $field;

  /** @var ?string */
  public $value;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Rules\Rule');
    $ruleaction = new \App\Models\Rules\Ruleaction();
    $this->definitions = $ruleaction->getDefinitions(true);

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

    $types = \App\Models\Definitions\Ruleaction::getActiontypeArray();
    if (
        Validation::attrNumericVal('action_type')->isValid($data) &&
        isset($data->action_type) &&
        isset($types[intval($data->action_type)])
    )
    {
      $this->action_type = intval($data->action_type);
    } else {
      throw new \Exception('Wrong data request', 400);
    }

    if (
        Validation::attrStr('field')->isValid($data) &&
        isset($data->field)
    )
    {
      $this->field = $data->field;
    }

    if (
        Validation::attrStr('value')->isValid($data) &&
        isset($data->value)
    )
    {
      $this->value = $data->value;
    }
  }

  /**
   * @return array{rule_id?: \App\Models\Rules\Rule, action_type?: int, field?: string, value?: string}
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
   * @param-out array{rule_id?: \App\Models\Rules\Rule, action_type?: int, field?: string, value?: string} $data
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
