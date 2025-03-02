<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostProject extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?string */
  public $code;

  /** @var ?string */
  public $content;

  /** @var ?\App\Models\Projecttype */
  public $type;

  /** @var ?\App\Models\Projectstate */
  public $state;

  /** @var ?bool */
  public $show_on_global_gantt;

  /** @var ?\App\Models\User */
  public $user;

  /** @var ?\App\Models\Group */
  public $group;

  /** @var ?string */
  public $plan_start_date;

  /** @var ?string */
  public $plan_end_date;

  /** @var ?string */
  public $real_start_date;

  /** @var ?string */
  public $real_end_date;

  /** @var ?string */
  public $comment;

  /** @var ?int */
  public $percent_done;

  /** @var ?int */
  public $priority;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Project');
    $project = new \App\Models\Project();
    $this->definitions = $project->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrStr('code')->isValid($data) &&
        isset($data->code)
    )
    {
      $this->code = $data->code;
    }

    if (
        Validation::attrStr('content')->isValid($data) &&
        isset($data->content)
    )
    {
      $this->content = $data->content;
    }

    if (
        Validation::attrNumericVal('type')->isValid($data) &&
        isset($data->type)
    )
    {
      $type = \App\Models\Projecttype::where('id', $data->type)->first();
      if (!is_null($type))
      {
        $this->type = $type;
      }
      elseif (intval($data->type) == 0)
      {
        $emptyType = new \App\Models\Projecttype();
        $emptyType->id = 0;
        $this->type = $emptyType;
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
        Validation::attrStr('show_on_global_gantt')->isValid($data) &&
        isset($data->show_on_global_gantt) &&
        $data->show_on_global_gantt == 'on'
    )
    {
      $this->show_on_global_gantt = true;
    } else {
      $this->show_on_global_gantt = false;
    }

    $this->user = $this->setUser($data);

    $this->group = $this->setGroup($data);

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

    $this->comment = $this->setComment($data);

    if (
        Validation::attrNumericVal('percent_done')->isValid($data) &&
        isset($data->percent_done)
    )
    {
      $this->percent_done = intval($data->percent_done);
    }

    if (
        Validation::attrNumericVal('priority')->isValid($data) &&
        isset($data->priority)
    )
    {
      $this->priority = intval($data->priority);
    }

    $this->is_recursive = $this->setIsrecursive($data);
  }

  /**
   * @return array{name?: string, code?: string, content?: string, type?: \App\Models\Projecttype,
   *               state?: \App\Models\Projectstate, show_on_global_gantt?: bool, user?: \App\Models\User,
   *               group?: \App\Models\Group, plan_start_date?: string, plan_end_date?: string,
   *               real_start_date?: string, real_end_date?: string, comment?: string, percent_done?: int,
   *               priority?: int, is_recursive?: bool}
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
   * @param-out array{name?: string, code?: string, content?: string, type?: \App\Models\Projecttype,
   *                  state?: \App\Models\Projectstate, show_on_global_gantt?: bool, user?: \App\Models\User,
   *                  group?: \App\Models\Group, plan_start_date?: string, plan_end_date?: string,
   *                  real_start_date?: string, real_end_date?: string, comment?: string, percent_done?: int,
   *                  priority?: int, is_recursive?: bool} $data
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
