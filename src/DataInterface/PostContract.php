<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostContract extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $num;

  /** @var ?\App\Models\State */
  public $state;

  /** @var ?\App\Models\Contracttype */
  public $type;

  /** @var ?string */
  public $begin_date;

  /** @var ?string */
  public $accounting_number;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_recursive;

  /** @var ?int */
  public $renewal;

  /** @var ?int */
  public $duration;

  /** @var ?int */
  public $notice;

  /** @var ?int */
  public $periodicity;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Contract');
    $contract = new \App\Models\Contract();
    $this->definitions = $contract->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrStrNotempty('num')->isValid($data) &&
        isset($data->num)
    )
    {
      $this->num = $data->num;
    }

    $this->state = $this->setState($data);

    if (
        Validation::attrNumericVal('type')->isValid($data) &&
        isset($data->type)
    )
    {
      $type = \App\Models\Contracttype::where('id', $data->type)->first();
      if (!is_null($type))
      {
        $this->type =  $type;
      }
      elseif (intval($data->type) == 0)
      {
        $emptyType = new \App\Models\Contracttype();
        $emptyType->id = 0;
        $this->type = $emptyType;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrDate('begin_date')->isValid($data) &&
        isset($data->begin_date)
    )
    {
      $this->begin_date = $data->begin_date;
    }

    if (
        Validation::attrStrNotempty('accounting_number')->isValid($data) &&
        isset($data->accounting_number)
    )
    {
      $this->accounting_number = $data->accounting_number;
    }

    $this->comment = $this->setComment($data);

    $this->is_recursive = $this->setIsrecursive($data);

    if (
        Validation::attrNumericVal('renewal')->isValid($data) &&
        isset($data->renewal)
    )
    {
      $this->renewal = intval($data->renewal);
    }

    if (
        Validation::attrNumericVal('duration')->isValid($data) &&
        isset($data->duration)
    )
    {
      $this->duration = intval($data->duration);
    }

    if (
        Validation::attrNumericVal('notice')->isValid($data) &&
        isset($data->notice)
    )
    {
      $this->notice = intval($data->notice);
    }

    if (
        Validation::attrNumericVal('periodicity')->isValid($data) &&
        isset($data->periodicity)
    )
    {
      $this->periodicity = intval($data->periodicity);
    }
  }

  /**
   * @return array{name?: string, num?: string, state?: \App\Models\State, type?: \App\Models\Contracttype,
   *               begin_date?: string, accounting_number?: string, comment?: string, is_recursive?: bool,
   *               renewal?: int, duration?: int, notice?: int, periodicity?: int}
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
   * @param-out array{name?: string, num?: string, state?: \App\Models\State, type?: \App\Models\Contracttype,
   *                  begin_date?: string, accounting_number?: string, comment?: string, is_recursive?: bool,
   *                  renewal?: int, duration?: int, notice?: int, periodicity?: int} $data
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
