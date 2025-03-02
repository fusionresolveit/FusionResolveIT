<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostProjecttasktemplate extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Projectstate */
  public $state;

  /** @var ?\App\Models\Projecttasktype */
  public $type;

  /** @var ?\App\Models\Projecttask */
  public $projecttasks;

  /** @var ?int */
  public $percent_done;

  /** @var ?bool */
  public $is_milestone;

  /** @var ?string */
  public $plan_start_date;

  /** @var ?string */
  public $real_start_date;

  /** @var ?string */
  public $plan_end_date;

  /** @var ?string */
  public $real_end_date;

  /** @var ?int */
  public $planned_duration;

  /** @var ?int */
  public $effective_duration;

  /** @var ?string */
  public $description;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Projecttasktemplate');
    $projecttasktemplate = new \App\Models\Projecttasktemplate();
    $this->definitions = $projecttasktemplate->getDefinitions();

    $this->name = $this->setName($data);

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
        Validation::attrNumericVal('projecttasks')->isValid($data) &&
        isset($data->projecttasks)
    )
    {
      $projecttasks = \App\Models\Projecttask::where('id', $data->projecttasks)->first();
      if (!is_null($projecttasks))
      {
        $this->projecttasks = $projecttasks;
      }
      elseif (intval($data->projecttasks) == 0)
      {
        $emptyProjecttasks = new \App\Models\Projecttask();
        $emptyProjecttasks->id = 0;
        $this->projecttasks = $emptyProjecttasks;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    if (
        Validation::attrNumericVal('percent_done')->isValid($data) &&
        isset($data->percent_done)
    )
    {
      $this->percent_done = intval($data->percent_done);
    }

    if (
        Validation::attrStr('is_milestone')->isValid($data) &&
        isset($data->is_milestone) &&
        $data->is_milestone == 'on'
    )
    {
      $this->is_milestone = true;
    } else {
      $this->is_milestone = false;
    }

    if (
        Validation::attrDate('plan_start_date')->isValid($data) &&
        isset($data->plan_start_date)
    )
    {
      $this->plan_start_date = $data->plan_start_date;
    }

    if (
        Validation::attrDate('real_start_date')->isValid($data) &&
        isset($data->real_start_date)
    )
    {
      $this->real_start_date = $data->real_start_date;
    }

    if (
        Validation::attrDate('plan_end_date')->isValid($data) &&
        isset($data->plan_end_date)
    )
    {
      $this->plan_end_date = $data->plan_end_date;
    }

    if (
        Validation::attrDate('real_end_date')->isValid($data) &&
        isset($data->real_end_date)
    )
    {
      $this->real_end_date = $data->real_end_date;
    }

    if (
        Validation::attrNumericVal('planned_duration')->isValid($data) &&
        isset($data->planned_duration)
    )
    {
      $this->planned_duration = intval($data->planned_duration);
    }

    if (
        Validation::attrNumericVal('effective_duration')->isValid($data) &&
        isset($data->effective_duration)
    )
    {
      $this->effective_duration = intval($data->effective_duration);
    }

    if (
        Validation::attrStr('description')->isValid($data) &&
        isset($data->description)
    )
    {
      $this->description = $data->description;
    }

    $this->comment = $this->setComment($data);

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, state?: \App\Models\Projectstate, type?: \App\Models\Projecttasktype,
   *               projecttasks?: \App\Models\Projecttask, percent_done?: int, is_milestone?: bool,
   *               plan_start_date?: string, real_start_date?: string, plan_end_date?: string,
   *               real_end_date?: string, planned_duration?: int, effective_duration?: int,
   *               description?: string, comment?: string, is_recursive?: bool}
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
   * @param-out array{name?: string, state?: \App\Models\Projectstate, type?: \App\Models\Projecttasktype,
   *                  projecttasks?: \App\Models\Projecttask, percent_done?: int, is_milestone?: bool,
   *                  plan_start_date?: string, real_start_date?: string, plan_end_date?: string,
   *                  real_end_date?: string, planned_duration?: int, effective_duration?: int,
   *                  description?: string, comment?: string, is_recursive?: bool} $data
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
