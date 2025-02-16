<?php

declare(strict_types=1);

namespace App\DataInterface;

use App\v1\Controllers\Fusioninventory\Validation;

class PostGroup extends Post
{
  /** @var ?string */
  public $name;

  /** @var ?\App\Models\Group */
  public $child;

  /** @var ?string */
  public $comment;

  /** @var ?bool */
  public $is_requester;

  /** @var ?bool */
  public $is_watcher;

  /** @var ?bool */
  public $is_assign;

  /** @var ?bool */
  public $is_task;

  /** @var ?bool */
  public $is_notify;

  /** @var ?bool */
  public $is_manager;

  /** @var ?bool */
  public $is_itemgroup;

  /** @var ?bool */
  public $is_usergroup;

  /** @var ?bool */
  public $is_recursive;

  public function __construct(object $data)
  {
    $this->loadRights('App\Models\Group');
    $group = new \App\Models\Group();
    $this->definitions = $group->getDefinitions();

    $this->name = $this->setName($data);

    if (
        Validation::attrNumericVal('child')->isValid($data) &&
        isset($data->child)
    )
    {
      $child = \App\Models\Group::where('id', $data->child)->first();
      if (!is_null($child))
      {
        $this->child = $child;
      }
      elseif (intval($data->child) == 0)
      {
        $emptyChild = new \App\Models\Group();
        $emptyChild->id = 0;
        $this->child = $emptyChild;
      } else {
        throw new \Exception('Wrong data request', 400);
      }
    }

    $this->comment = $this->setComment($data);

    if (
        Validation::attrStr('is_requester')->isValid($data) &&
        isset($data->is_requester) &&
        $data->is_requester == 'on'
    )
    {
      $this->is_requester = true;
    } else {
      $this->is_requester = false;
    }

    if (
        Validation::attrStr('is_watcher')->isValid($data) &&
        isset($data->is_watcher) &&
        $data->is_watcher == 'on'
    )
    {
      $this->is_watcher = true;
    } else {
      $this->is_watcher = false;
    }

    if (
        Validation::attrStr('is_assign')->isValid($data) &&
        isset($data->is_assign) &&
        $data->is_assign == 'on'
    )
    {
      $this->is_assign = true;
    } else {
      $this->is_assign = false;
    }

    if (
        Validation::attrStr('is_task')->isValid($data) &&
        isset($data->is_task) &&
        $data->is_task == 'on'
    )
    {
      $this->is_task = true;
    } else {
      $this->is_task = false;
    }

    if (
        Validation::attrStr('is_notify')->isValid($data) &&
        isset($data->is_notify) &&
        $data->is_notify == 'on'
    )
    {
      $this->is_notify = true;
    } else {
      $this->is_notify = false;
    }

    if (
        Validation::attrStr('is_manager')->isValid($data) &&
        isset($data->is_manager) &&
        $data->is_manager == 'on'
    )
    {
      $this->is_manager = true;
    } else {
      $this->is_manager = false;
    }

    if (
        Validation::attrStr('is_itemgroup')->isValid($data) &&
        isset($data->is_itemgroup) &&
        $data->is_itemgroup == 'on'
    )
    {
      $this->is_itemgroup = true;
    } else {
      $this->is_itemgroup = false;
    }

    if (
        Validation::attrStr('is_usergroup')->isValid($data) &&
        isset($data->is_usergroup) &&
        $data->is_usergroup == 'on'
    )
    {
      $this->is_usergroup = true;
    } else {
      $this->is_usergroup = false;
    }

    if (
        Validation::attrStr('is_recursive')->isValid($data) &&
        isset($data->is_recursive) &&
        $data->is_recursive == 'on'
    )
    {
      $this->is_recursive = true;
    } else {
      $this->is_recursive = false;
    }
  }

  /**
   * @return array{name?: string, child?: \App\Models\Group, comment?: string, is_requester?: bool,
   *               is_watcher?: bool, is_assign?: bool, is_task?: bool, is_notify?: bool, is_manager?: bool,
   *               is_itemgroup?: bool, is_usergroup?: bool, is_recursive?: bool}
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
   * @param-out array{name?: string, child?: \App\Models\Group, comment?: string, is_requester?: bool,
   *                  is_watcher?: bool, is_assign?: bool, is_task?: bool, is_notify?: bool, is_manager?: bool,
   *                  is_itemgroup?: bool, is_usergroup?: bool, is_recursive?: bool} $data
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
