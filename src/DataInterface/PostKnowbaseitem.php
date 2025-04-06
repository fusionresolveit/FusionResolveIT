<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostKnowbaseitem extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Category */
  public $category;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Knowbaseitem');
    $knowbaseitem = new \App\Models\Knowbaseitem();
    $this->definitions = $knowbaseitem->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrNumericVal('category')->isValid($data) &&
        isset($data->category)
    )
    {
      $category = \App\Models\Category::where('id', $data->category)->first();
      if (!is_null($category))
      {
        $this->category = $category;
      }
      elseif (intval($data->category) == 0)
      {
        $emptyCategory = new \App\Models\Category();
        $emptyCategory->id = 0;
        $this->category = $emptyCategory;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }
  }

  /**
   * @return array{name?: string, category?: \App\Models\Category}
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
   * @param-out array{name?: string, category?: \App\Models\Category} $data
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
