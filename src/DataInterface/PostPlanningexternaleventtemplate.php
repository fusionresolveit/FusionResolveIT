<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostPlanningexternaleventtemplate extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?int */
  public $state;

  /** @var ?\App\Models\Planningeventcategory */
  public $category;

  /** @var ?bool */
  public $background;

  /** @var ?int */
  public $duration;

  /** @var ?int */
  public $before_time;

  /** @var ?string */
  public $rrule;

  /** @var ?string */
  public $text;

  /** @var ?string */
  public $comment;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Planningexternaleventtemplate');
    $planningexternaleventtemplate = new \App\Models\Planningexternaleventtemplate();
    $this->definitions = $planningexternaleventtemplate->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrNumericVal('state')->isValid($data) &&
        isset($data->state)
    )
    {
      $this->state = intval($data->state);
    }

    if (
        Validation::attrNumericVal('category')->isValid($data) &&
        isset($data->category)
    )
    {
      $category = \App\Models\Planningeventcategory::where('id', $data->category)->first();
      if (!is_null($category))
      {
        $this->category = $category;
      }
      elseif (intval($data->category) == 0)
      {
        $emptyCategory = new \App\Models\Planningeventcategory();
        $emptyCategory->id = 0;
        $this->category = $emptyCategory;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrStr('background')->isValid($data) &&
        isset($data->background) &&
        $data->background == 'on'
    )
    {
      $this->background = true;
    } else {
      $this->background = false;
    }

    if (
        Validation::attrNumericVal('duration')->isValid($data) &&
        isset($data->duration)
    )
    {
      $this->duration = intval($data->duration);
    }

    if (
        Validation::attrNumericVal('before_time')->isValid($data) &&
        isset($data->before_time)
    )
    {
      $this->before_time = intval($data->before_time);
    }

    if (
        Validation::attrStr('rrule')->isValid($data) &&
        isset($data->rrule)
    )
    {
      $this->rrule = $data->rrule;
    }

    if (
        Validation::attrStr('text')->isValid($data) &&
        isset($data->text)
    )
    {
      $this->text = $data->text;
    }

    $this->comment = $this->setComment($data);
  }

  /**
   * @return array{name?: string, state?: int, category?: \App\Models\Planningeventcategory,
   *               background?: bool, duration?: int, before_time?: int, rrule?: string, text?: string,
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
   * @param-out array{name?: string, state?: int, category?: \App\Models\Planningeventcategory,
   *                  background?: bool, duration?: int, before_time?: int, rrule?: string, text?: string,
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
