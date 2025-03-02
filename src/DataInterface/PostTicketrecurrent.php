<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostTicketrecurrent extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_recursive;

  /** @var ?bool */
  public $is_active;

  /** @var ?string */
  public $begin_date;

  /** @var ?string */
  public $end_date;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Ticketrecurrent');
    $ticketrecurrent = new \App\Models\Ticketrecurrent();
    $this->definitions = $ticketrecurrent->getDefinitions();

    $this->name = $this->setName($data);

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
        Validation::attrDate('begin_date')->isValid($data) &&
        isset($data->begin_date)
    )
    {
      $this->begin_date = $data->begin_date;
    }

    if (
        Validation::attrDate('end_date')->isValid($data) &&
        isset($data->end_date)
    )
    {
      $this->end_date = $data->end_date;
    }
  }

  /**
   * @return array{name?: string, comment?: string, is_recursive?: bool, is_active?: bool,
   *               begin_date?: string, end_date?: string}
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
   * @param-out array{name?: string, comment?: string, is_recursive?: bool, is_active?: bool,
   *                  begin_date?: string, end_date?: string} $data
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
