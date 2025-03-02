<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostDcroom extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Datacenter */
  public $datacenter;

  /** @var ?\App\Models\Location */
  public $location;

  /** @var ?string */
  public $vis_cols;

  /** @var ?string */
  public $vis_rows;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Dcroom');
    $dcroom = new \App\Models\Dcroom();
    $this->definitions = $dcroom->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrNumericVal('datacenter')->isValid($data) &&
        isset($data->datacenter)
    )
    {
      $datacenter = \App\Models\Datacenter::where('id', $data->datacenter)->first();
      if (!is_null($datacenter))
      {
        $this->datacenter = $datacenter;
      }
      elseif (intval($data->datacenter) == 0)
      {
        $emptyDatacenter = new \App\Models\Datacenter();
        $emptyDatacenter->id = 0;
        $this->datacenter = $emptyDatacenter;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->location = $this->setLocation($data);

    if (
        Validation::attrStr('vis_cols')->isValid($data) &&
        isset($data->vis_cols)
    )
    {
      $this->vis_cols = $data->vis_cols;
    }

    if (
        Validation::attrStr('vis_rows')->isValid($data) &&
        isset($data->vis_rows)
    )
    {
      $this->vis_rows = $data->vis_rows;
    }

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, datacenter?: \App\Models\Datacenter, location?: \App\Models\Location,
   *               vis_cols?: string, vis_rows?: string, is_recursive?: bool}
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
   * @param-out array{name?: string, datacenter?: \App\Models\Datacenter, location?: \App\Models\Location,
   *                  vis_cols?: string, vis_rows?: string, is_recursive?: bool} $data
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
