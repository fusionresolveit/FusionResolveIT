<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostNotification extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $item_type;

  /** @var ?bool */
  public $is_active;

  /** @var ?bool */
  public $allow_response;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Notification');
    $notification = new \App\Models\Notification();
    $this->definitions = $notification->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrStrNotempty('item_type')->isValid($data) &&
        isset($data->item_type)
    )
    {
      $this->item_type = $data->item_type;
    }

    if (
        Validation::attrStr('is_active')->isValid($data) &&
        isset($data->is_active) &&
        $data->is_active == 'on'
    )
    {
      $this->is_active = true;
    } else {
      $this->is_active = true;
    }

    if (
        Validation::attrStr('allow_response')->isValid($data) &&
        isset($data->allow_response) &&
        $data->allow_response == 'on'
    )
    {
      $this->allow_response = true;
    } else {
      $this->allow_response = true;
    }

    $this->comment = $this->setComment($data);

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, item_type?: string, is_active?: bool, allow_response?: bool, comment?: string,
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
   * @param-out array{name?: string, item_type?: string, is_active?: bool, allow_response?: bool, comment?: string,
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
