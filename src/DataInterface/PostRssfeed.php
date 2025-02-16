<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostRssfeed extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\User */
  public $user;

  /** @var ?string */
  public $url;

  /** @var ?bool */
  public $is_active;

  /** @var ?bool */
  public $have_error;

  /** @var ?int */
  public $max_items;

  /** @var ?string */
  public $comment;

  /** @var ?int */
  public $refresh_rate;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Rssfeed');
    $rssfeed = new \App\Models\Rssfeed();
    $this->definitions = $rssfeed->getDefinitions();

    $this->name = $this->setName($data);

    $this->user = $this->setUser($data);

    if (
        Validation::attrStr('url')->isValid($data) &&
        isset($data->url)
    )
    {
      $this->url = $data->url;
    }

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
        Validation::attrStr('have_error')->isValid($data) &&
        isset($data->have_error) &&
        $data->have_error == 'on'
    )
    {
      $this->have_error = true;
    } else {
      $this->have_error = false;
    }

    if (
        Validation::attrNumericVal('max_items')->isValid($data) &&
        isset($data->max_items)
    )
    {
      $this->max_items = intval($data->max_items);
    }

    $this->comment = $this->setComment($data);

    if (
        Validation::attrNumericVal('refresh_rate')->isValid($data) &&
        isset($data->refresh_rate)
    )
    {
      $this->refresh_rate = intval($data->refresh_rate);
    }
  }

  /**
   * @return array{name?: string, user?: \App\Models\User, url?: string, is_active?: bool,
   *               have_error?: bool, max_items?: int, comment?: string, refresh_rate?: int}
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
   * @param-out array{name?: string, user?: \App\Models\User, url?: string, is_active?: bool,
   *                  have_error?: bool, max_items?: int, comment?: string, refresh_rate?: int} $data
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
