<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostAlert extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $message;

  /** @var ?int */
  public $type;

  /** @var ?string */
  public $begin_date;

  /** @var ?string */
  public $end_date;

  /** @var ?bool */
  public $is_recursive;

  /** @var ?bool */
  public $is_displayed_onlogin;

  /** @var ?bool */
  public $is_displayed_oncentral;

  /** @var ?bool */
  public $is_active;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Alert');
    $alert = new \App\Models\Alert();
    $this->definitions = $alert->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrStrNotempty('message')->isValid($data) &&
        isset($data->message)
    )
    {
      $this->message = $data->message;
    }

    if (
        Validation::attrNumericVal('type')->isValid($data) &&
        isset($data->type)
    )
    {
      $this->type = intval($data->type);
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

    $this->is_recursive = $this->setIsrecursive($data);

    if (
        Validation::attrStr('is_displayed_onlogin')->isValid($data) &&
        isset($data->is_displayed_onlogin) &&
        $data->is_displayed_onlogin == 'on'
    )
    {
      $this->is_displayed_onlogin = true;
    } else {
      $this->is_displayed_onlogin = true;
    }

    if (
        Validation::attrStr('is_displayed_oncentral')->isValid($data) &&
        isset($data->is_displayed_oncentral) &&
        $data->is_displayed_oncentral == 'on'
    )
    {
      $this->is_displayed_oncentral = true;
    } else {
      $this->is_displayed_oncentral = true;
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
  }

  /**
   * @return array{name?: string, message?: string, type?: int, begin_date?: string, end_date?: string,
   *               is_recursive?: bool, is_displayed_onlogin?: bool,
   *               is_displayed_oncentral?: bool, is_active?: bool}
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
   * @param-out array{name?: string, message?: string, type?: int, begin_date?: string, end_date?: string,
   *                  is_recursive?: bool, is_displayed_onlogin?: bool,
   *                  is_displayed_oncentral?: bool, is_active?: bool} $data
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
