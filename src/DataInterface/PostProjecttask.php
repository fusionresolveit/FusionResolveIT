<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostProjecttask extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Projecttask */
  public $parent;

  /** @var ?\App\Models\Projectstate */
  public $state;

  /** @var ?\App\Models\Projecttasktype */
  public $type;

  /** @var ?string */
  public $plan_start_date;

  /** @var ?string */
  public $plan_end_date;

  /** @var ?int */
  public $planned_duration;

  /** @var ?string */
  public $real_start_date;

  /** @var ?string */
  public $real_end_date;

  /** @var ?int */
  public $effective_duration;

  /** @var ?int */
  public $percent_done;

  /** @var ?string */
  public $comment;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Projecttask');
    $projecttask = new \App\Models\Projecttask();
    $this->definitions = $projecttask->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrNumericVal('parent')->isValid($data) &&
        isset($data->parent)
    )
    {
      $parent = \App\Models\Projecttask::where('id', $data->parent)->first();
      if (!is_null($parent))
      {
        $this->parent = $parent;
      }
      elseif (intval($data->parent) == 0)
      {
        $emptyParent = new \App\Models\Projecttask();
        $emptyParent->id = 0;
        $this->parent = $emptyParent;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('state')->isValid($data) &&
        isset($data->state)
    )
    {
      $state = \App\Models\Projectstate::where('id', $data->state)->first();
      if (!is_null($state))
      {
        $this->state = $state;
      }
      elseif (intval($data->state) == 0)
      {
        $emptyState = new \App\Models\Projectstate();
        $emptyState->id = 0;
        $this->state = $emptyState;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('type')->isValid($data) &&
        isset($data->type)
    )
    {
      $type = \App\Models\Projecttasktype::where('id', $data->type)->first();
      if (!is_null($type))
      {
        $this->type = $type;
      }
      elseif (intval($data->type) == 0)
      {
        $emptyType = new \App\Models\Projecttasktype();
        $emptyType->id = 0;
        $this->type = $emptyType;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrDate('plan_start_date')->isValid($data) &&
        isset($data->plan_start_date)
    )
    {
      $this->plan_start_date = $data->plan_start_date;
    }

    if (
        Validation::attrDate('plan_end_date')->isValid($data) &&
        isset($data->plan_end_date)
    )
    {
      $this->plan_end_date = $data->plan_end_date;
    }

    if (
        Validation::attrNumericVal('planned_duration')->isValid($data) &&
        isset($data->planned_duration)
    )
    {
      $this->planned_duration = intval($data->planned_duration);
    }

    if (
        Validation::attrDate('real_start_date')->isValid($data) &&
        isset($data->real_start_date)
    )
    {
      $this->real_start_date = $data->real_start_date;
    }

    if (
        Validation::attrDate('real_end_date')->isValid($data) &&
        isset($data->real_end_date)
    )
    {
      $this->real_end_date = $data->real_end_date;
    }

    if (
        Validation::attrNumericVal('effective_duration')->isValid($data) &&
        isset($data->effective_duration)
    )
    {
      $this->effective_duration = intval($data->effective_duration);
    }

    if (
        Validation::attrNumericVal('percent_done')->isValid($data) &&
        isset($data->percent_done)
    )
    {
      $this->percent_done = intval($data->percent_done);
    }

    $this->comment = $this->setComment($data);
  }

  /**
   * @return array{name?: string, parent?: \App\Models\Projecttask, state?: \App\Models\Projectstate,
   *               type?: \App\Models\Projecttasktype, plan_start_date?: string, plan_end_date?: string,
   *               planned_duration?: int, real_start_date?: string, real_end_date?: string,
   *               effective_duration?: int, percent_done?: int, comment?: string}
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
   * @param-out array{name?: string, parent?: \App\Models\Projecttask, state?: \App\Models\Projectstate,
   *                  type?: \App\Models\Projecttasktype, plan_start_date?: string, plan_end_date?: string,
   *                  planned_duration?: int, real_start_date?: string, real_end_date?: string,
   *                  effective_duration?: int, percent_done?: int, comment?: string} $data
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
