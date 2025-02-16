<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostFieldblacklist extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_recursive;

  /** @var ?string */
  public $field;

  /** @var ?string */
  public $value;

  /** @var ?string */
  public $item_type;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Fieldblacklist');
    $fieldblacklist = new \App\Models\Fieldblacklist();
    $this->definitions = $fieldblacklist->getDefinitions();

    $this->name = $this->setName($data);

    $this->comment = $this->setComment($data);

    $this->is_recursive = $this->setIsrecursive($data);

    if (
        Validation::attrStrNotempty('field')->isValid($data) &&
        isset($data->field)
    )
    {
      $this->field = $data->field;
    }

    if (
        Validation::attrStrNotempty('value')->isValid($data) &&
        isset($data->value)
    )
    {
      $this->value = $data->value;
    }

    if (
        Validation::attrStrNotempty('item_type')->isValid($data) &&
        isset($data->item_type) &&
        class_exists($data->item_type)
    )
    {
      $this->item_type = $data->item_type;
    }
  }

  /**
   * @return array{name?: string, comment?: string, is_recursive?: bool, field?: string, value?: string,
   *               item_type?: string}
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
   * @param-out array{name?: string, comment?: string, is_recursive?: bool, field?: string, value?: string,
   *                  item_type?: string} $data
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
