<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostBusinesscriticity extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $comment;

  /** @var ?\App\Models\Entity */
  public $entity;

  /** @var ?bool */
  public $is_recursive;

  /** @var ?\App\Models\Businesscriticity */
  public $businesscriticity;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Businesscriticity');
    $businesscriticity = new \App\Models\Businesscriticity();
    $this->definitions = $businesscriticity->getDefinitions();

    $this->name = $this->setName($data);

    $this->comment = $this->setComment($data);

    $this->entity = $this->setEntity($data);

    $this->is_recursive = $this->setIsrecursive($data);

    if (
        Validation::attrNumericVal('businesscriticity')->isValid($data) &&
        isset($data->businesscriticity)
    )
    {
      $businesscriticity = \App\Models\Businesscriticity::where('id', $data->businesscriticity)->first();
      if (!is_null($businesscriticity))
      {
        $this->businesscriticity = $businesscriticity;
      }
      elseif (intval($data->businesscriticity) == 0)
      {
        $emptyBusinesscriticity = new \App\Models\Businesscriticity();
        $emptyBusinesscriticity->id = 0;
        $this->businesscriticity = $emptyBusinesscriticity;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }
  }

  /**
   * @return array{name?: string, comment?: string, entity?: \App\Models\Entity, is_recursive?: bool}
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
   * @param-out array{name?: string, comment?: string, entity?: \App\Models\Entity, is_recursive?: bool} $data
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
