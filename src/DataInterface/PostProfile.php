<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostProfile extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?bool */
  public $is_default;

  /** @var ?string */
  public $interface;

  /** @var ?bool */
  public $create_ticket_on_login;

  /** @var ?string */
  public $comment;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Profile');
    $profile = new \App\Models\Profile();
    $this->definitions = $profile->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrStr('is_default')->isValid($data) &&
        isset($data->is_default) &&
        $data->is_default == 'on'
    )
    {
      $this->is_default = true;
    } else {
      $this->is_default = false;
    }

    if (
        Validation::attrStr('interface')->isValid($data) &&
        isset($data->interface)
    )
    {
      $this->interface = $data->interface;
    }

    if (
        Validation::attrStr('create_ticket_on_login')->isValid($data) &&
        isset($data->create_ticket_on_login) &&
        $data->create_ticket_on_login == 'on'
    )
    {
      $this->create_ticket_on_login = true;
    } else {
      $this->create_ticket_on_login = false;
    }

    $this->comment = $this->setComment($data);
  }

  /**
   * @return array{name?: string, is_default?: bool, interface?: string, create_ticket_on_login?: bool,
   *               comment?: string}
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
   * @param-out array{name?: string, is_default?: bool, interface?: string, create_ticket_on_login?: bool,
   *                  comment?: string} $data
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
