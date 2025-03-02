<?php

declare(strict_types=1);

namespace App\DataInterface;

class PostStandardDevicemodel extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $product_number;

  /** @var ?string */
  public $comment;

  public function __construct(object $data, string $modelname)
  {
    if (!class_exists($modelname))
    {
      return;
    }
    $this->loadRights(trim($modelname, '\\'));
    $item = new $modelname();

    if (method_exists($item, 'getDefinitions'))
    {
      $this->name = $this->setName($data);
      $this->product_number = $this->setProductnumber($data);
      $this->comment = $this->setComment($data);
    }
  }

  /**
   * @return array{name?: string, product_number?: string, comment?: string}
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
   * @param-out array{name?: string, product_number?: string, comment?: string} $data
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
