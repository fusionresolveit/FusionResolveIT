<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostLink extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $link;

  /** @var ?bool */
  public $open_window;

  /** @var ?string */
  public $data;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Link');
    $link = new \App\Models\Link();
    $this->definitions = $link->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrStr('link')->isValid($data) &&
        isset($data->link)
    )
    {
      $this->link = $data->link;
    }

    if (
        Validation::attrStr('open_window')->isValid($data) &&
        isset($data->open_window) &&
        $data->open_window == 'on'
    )
    {
      $this->open_window = true;
    } else {
      $this->open_window = false;
    }

    if (
        Validation::attrStr('data')->isValid($data) &&
        isset($data->data)
    )
    {
      $this->data = $data->data;
    }

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, link?: string, open_window?: bool, data?: string,
   *               is_recursive?: bool}
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
   * @param-out array{name?: string, link?: string, open_window?: bool, data?: string,
   *                  is_recursive?: bool} $data
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
