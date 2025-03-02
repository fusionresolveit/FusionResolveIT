<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostSolution extends Post
{
  /** @var ?string */
  public $solution;

  /** @var ?int */
  public $item_id;

  /** @var ?string */
  public $item_type;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Solution');
    $solution = new \App\Models\Solution();
    $this->definitions = $solution->getDefinitions();

    if (
        Validation::attrStr('solution')->isValid($data) &&
        isset($data->solution)
    )
    {
      $this->solution = $data->solution;
    }

    if (
        Validation::attrNumericVal('item_id')->isValid($data) &&
        isset($data->item_id)
    )
    {
      $this->item_id = intval($data->item_id);
    }

    if (
        Validation::attrStr('item_type')->isValid($data) &&
        isset($data->item_type)
    )
    {
      $this->item_type = $data->item_type;
    }
  }

  /**
   * @return array{solution?: string, item_id?: int, item_type?: string}
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
   * @param-out array{solution?: string, item_id?: int, item_type?: string} $data
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
