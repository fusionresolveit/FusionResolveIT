<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostFormForm extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $content;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_recursive;

  /** @var ?bool */
  public $is_active;

  /** @var ?\App\Models\Category */
  public $category;

  /** @var ?bool */
  public $is_homepage;

  /** @var ?string */
  public $icon;

  /** @var ?string */
  public $icon_color;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Forms\Form');
    $form = new \App\Models\Forms\Form();
    $this->definitions = $form->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrStr('content')->isValid($data) &&
        isset($data->content)
    )
    {
      $this->content = $data->content;
    }

    $this->comment = $this->setComment($data);

    $this->is_recursive = $this->setIsrecursive($data);

    if (
        Validation::attrStr('is_active')->isValid($data) &&
        isset($data->is_active) &&
        $data->is_active == 'on'
    )
    {
      $this->is_active = true;
    } else {
      $this->is_active = false;
    }

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

    if (
        Validation::attrStr('is_homepage')->isValid($data) &&
        isset($data->is_homepage) &&
        $data->is_homepage == 'on'
    )
    {
      $this->is_homepage = true;
    } else {
      $this->is_homepage = false;
    }
    
    if (
        Validation::attrStr('icon')->isValid($data) &&
        isset($data->icon)
    )
    {
      $this->icon = $data->icon;
    }

    if (
        Validation::attrStr('icon_color')->isValid($data) &&
        isset($data->icon_color)
    )
    {
      $this->icon_color = $data->icon_color;
    }
  }

  /**
   * @return array{name?: string, content?: string, comment?: string, is_recursive?: bool, is_active?: bool,
   *               category?: \App\Models\Category, is_homepage?: bool, icon?: string, icon_color?: bool}
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
   * @param-out array{name?: string, content?: string, comment?: string, is_recursive?: bool, is_active?: bool,
   *                  category?: \App\Models\Category, is_homepage?: bool, icon?: string, icon_color?: bool} $data
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
