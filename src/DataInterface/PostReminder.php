<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostReminder extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $text;

  /** @var ?string */
  public $begin_view_date;

  /** @var ?string */
  public $end_view_date;

  /** @var ?int */
  public $state;

  /** @var ?bool */
  public $is_planned;

  /** @var ?string */
  public $begin;

  /** @var ?string */
  public $end;

  /** @var ?\App\Models\User */
  public $user;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Reminder');
    $reminder = new \App\Models\Reminder();
    $this->definitions = $reminder->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrStr('text')->isValid($data) &&
        isset($data->text)
    )
     {
      $this->text = $data->text;
    }

    if (
        Validation::attrDate('begin_view_date')->isValid($data) &&
        isset($data->begin_view_date)
    )
    {
      $this->begin_view_date = $data->begin_view_date;
    }

    if (
        Validation::attrDate('end_view_date')->isValid($data) &&
        isset($data->end_view_date)
    )
    {
      $this->end_view_date = $data->end_view_date;
    }

    if (
        Validation::attrNumericVal('state')->isValid($data) &&
        isset($data->state)
    )
    {
      $this->state = intval($data->state);
    }

    if (
        Validation::attrStr('is_planned')->isValid($data) &&
        isset($data->is_planned) &&
        $data->is_planned == 'on'
    )
    {
      $this->is_planned = true;
    } else {
      $this->is_planned = false;
    }

    if (
        Validation::attrDate('begin')->isValid($data) &&
        isset($data->begin)
    )
    {
      $this->begin = $data->begin;
    }

    if (
        Validation::attrDate('end')->isValid($data) &&
        isset($data->end)
    )
    {
      $this->end = $data->end;
    }

    $this->user = $this->setUser($data);
  }

  /**
   * @return array{name?: string, text?: string, begin_view_date?: string, end_view_date?: string,
   *               state?: int, is_planned?: bool, begin?: string, end?: string, user?: \App\Models\User}
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
   * @param-out array{name?: string, text?: string, begin_view_date?: string, end_view_date?: string,
   *                  state?: int, is_planned?: bool, begin?: string, end?: string, user?: \App\Models\User} $data
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
