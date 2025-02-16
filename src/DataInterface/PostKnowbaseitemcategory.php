<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostKnowbaseitemcategory extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Knowbaseitemcategory */
  public $category;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Knowbaseitemcategory');
    $kicategory = new \App\Models\Knowbaseitemcategory();
    $this->definitions = $kicategory->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrNumericVal('category')->isValid($data) &&
        isset($data->category)
    )
    {
      $category = \App\Models\Knowbaseitemcategory::where('id', $data->category)->first();
      if (!is_null($category))
      {
        $this->category = $category;
      }
      elseif (intval($data->category) == 0)
      {
        $emptyCategory = new \App\Models\Knowbaseitemcategory();
        $emptyCategory->id = 0;
        $this->category = $emptyCategory;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->comment = $this->setComment($data);

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, category?: \App\Models\Knowbaseitemcategory,
   *               comment?: string, is_recursive?: bool}
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
   * @param-out array{name?: string, category?: \App\Models\Knowbaseitemcategory,
   *                  comment?: string, is_recursive?: bool} $data
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
