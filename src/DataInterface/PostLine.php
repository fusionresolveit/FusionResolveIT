<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostLine extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?bool */
  public $is_recursive;

  /** @var ?\App\Models\Location */
  public $location;

  /** @var ?\App\Models\Linetype */
  public $type;

  /** @var ?\App\Models\Lineoperator */
  public $operator;

  /** @var ?string */
  public $comment;

  /** @var ?\App\Models\State */
  public $state;

  /** @var ?string */
  public $caller_num;

  /** @var ?string */
  public $caller_name;

  /** @var ?\App\Models\User */
  public $user;

  /** @var ?\App\Models\Group */
  public $group;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Line');
    $line = new \App\Models\Line();
    $this->definitions = $line->getDefinitions();

    $this->name = $this->setName($data);

    $this->is_recursive = $this->setIsrecursive($data);

    $this->location = $this->setLocation($data);

    if (
        Validation::attrNumericVal('type')->isValid($data) &&
        isset($data->type)
    )
    {
      $type = \App\Models\Linetype::where('id', $data->type)->first();
      if (!is_null($type))
      {
        $this->type = $type;
      }
      elseif (intval($data->type) == 0)
      {
        $emptyType = new \App\Models\Linetype();
        $emptyType->id = 0;
        $this->type = $emptyType;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('operator')->isValid($data) &&
        isset($data->operator)
    )
    {
      $operator = \App\Models\Lineoperator::where('id', $data->operator)->first();
      if (!is_null($operator))
      {
        $this->operator = $operator;
      }
      elseif (intval($data->operator) == 0)
      {
        $emptyOperator = new \App\Models\Lineoperator();
        $emptyOperator->id = 0;
        $this->operator = $emptyOperator;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->comment = $this->setComment($data);

    $this->state = $this->setState($data);

    if (
        Validation::attrStr('caller_num')->isValid($data) &&
        isset($data->caller_num)
    )
    {
      $this->caller_num = $data->caller_num;
    }

    if (
        Validation::attrStr('caller_name')->isValid($data) &&
        isset($data->caller_name)
    )
    {
      $this->caller_name = $data->caller_name;
    }

    $this->user = $this->setUser($data);

    $this->group = $this->setGroup($data);
  }

  /**
   * @return array{name?: string, is_recursive?: bool, location?: \App\Models\Location,
   *               type?: \App\Models\Linetype, operator?: \App\Models\Lineoperator, comment?: string,
   *               state?: \App\Models\State, caller_num?: string, caller_name?: string,
   *               user?: \App\Models\User, group?: \App\Models\Group}
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
   * @param-out array{name?: string, is_recursive?: bool, location?: \App\Models\Location,
   *                  type?: \App\Models\Linetype, operator?: \App\Models\Lineoperator, comment?: string,
   *                  state?: \App\Models\State, caller_num?: string, caller_name?: string,
   *                  user?: \App\Models\User, group?: \App\Models\Group} $data
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
