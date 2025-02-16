<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostFieldunicity extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?bool */
  public $is_active;

  /** @var ?string */
  public $item_type;

  /** @var ?bool */
  public $action_refuse;

  /** @var ?bool */
  public $action_notify;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Fieldunicity');
    $fieldunicity = new \App\Models\Fieldunicity();
    $this->definitions = $fieldunicity->getDefinitions();

    $this->name = $this->setName($data);

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
        Validation::attrStrNotempty('item_type')->isValid($data) &&
        isset($data->item_type)
    )
    {
      $this->item_type = $data->item_type;
    }

    if (
        Validation::attrStr('action_refuse')->isValid($data) &&
        isset($data->action_refuse) &&
        $data->action_refuse == 'on'
    )
    {
      $this->action_refuse = true;
    } else {
      $this->action_refuse = false;
    }

    if (
        Validation::attrStr('action_notify')->isValid($data) &&
        isset($data->action_notify) &&
        $data->action_notify == 'on'
    )
    {
      $this->action_notify = true;
    } else {
      $this->action_notify = false;
    }

    $this->comment = $this->setComment($data);

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, is_active?: bool, item_type?: string, action_refuse?: bool, action_notify?: bool,
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
   * @param-out array{name?: string, is_active?: bool, item_type?: string, action_refuse?: bool, action_notify?: bool,
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
