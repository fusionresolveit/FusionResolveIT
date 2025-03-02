<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostSavedsearch extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?int */
  public $is_private;

  /** @var ?int */
  public $do_count;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Savedsearch');
    $savedsearch = new \App\Models\Savedsearch();
    $this->definitions = $savedsearch->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrNumericVal('is_private')->isValid($data) &&
        isset($data->is_private)
    )
    {
      $this->is_private = $data->is_private;
    }

    if (
        Validation::attrNumericVal('do_count')->isValid($data) &&
        isset($data->do_count)
    )
    {
      $this->do_count = $data->do_count;
    }
  }

  /**
   * @return array{name?: string, is_private?: int, do_count?: int}
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
   * @param-out array{name?: string, is_private?: int, do_count?: int} $data
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
