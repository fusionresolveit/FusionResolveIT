<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostBudget extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $value;

  /** @var ?string */
  public $begin_date;

  /** @var ?string */
  public $end_date;

  /** @var ?\App\Models\Budgettype */
  public $type;

  /** @var ?string */
  public $comment;

  /** @var ?\App\Models\Location */
  public $location;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Budget');
    $budget = new \App\Models\Budget();
    $this->definitions = $budget->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrStrNotempty('value')->isValid($data) &&
        isset($data->value)
    )
    {
      $this->value = $data->value;
    }

    if (
        Validation::attrDate('begin_date')->isValid($data) &&
        isset($data->begin_date)
    )
    {
      $this->begin_date = $data->begin_date;
    }

    if (
        Validation::attrDate('end_date')->isValid($data) &&
        isset($data->end_date)
    )
    {
      $this->end_date = $data->end_date;
    }

    if (
        Validation::attrNumericVal('type')->isValid($data) &&
        isset($data->type)
    )
    {
      $type = \App\Models\Budgettype::where('id', $data->type)->first();
      if (!is_null($type))
      {
        $this->type = $type;
      }
      elseif (intval($data->type) == 0)
      {
        $emptyBudgettype = new \App\Models\Budgettype();
        $emptyBudgettype->id = 0;
        $this->type = $emptyBudgettype;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->comment = $this->setComment($data);

    $this->location = $this->setLocation($data);

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, value?: string, begin_date?: string, end_date?: string, type?: \App\Models\Budgettype,
   *               comment?: string, location?: \App\Models\Location, is_recursive?: bool}
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
   * @param-out array{name?: string, value?: string, begin_date?: string, end_date?: string,
   *                  type?: \App\Models\Budgettype, comment?: string, location?: \App\Models\Location,
   *                  is_recursive?: bool} $data
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
